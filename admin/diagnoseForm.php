<?php
require_once '../_config.php';
require_once '../_include-v2.php';

// includeClass(array('InitialDiagnose.class.php'));
includeClass(array('Category.class.php','Diagnose.class.php',));
$diagnose = createObjAndAddToCol(new Diagnose());


$obj = $diagnose;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'diagnoseList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj);


if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $_POST['name'] = $rs[0]['name'] ;
    $_POST['orderList'] = $rs[0]['orderlist']; 
    $_POST['trShortDesc'] = $rs[0]['shortdescription'] ;

	if(!empty($rs[0]['parentkey'])){
         $rsCategory = $obj->getDataRowById($rs[0]['parentkey']);
         $_POST['categoryName'] = $rsCategory[0]['name'] ;
         $_POST['selCategory'] = $rsCategory[0]['pkey'] ;
     }

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>

        var diagnose = new Diagnose(tabID);

        prepareHandler(diagnose);

        var fieldValidation = {
            code: {
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.code[1]
                    },
                }
            },
//            orderList: { 
//                            validators: { 
//                            regexp: {
//                                    regexp: /^[0-9]+$/,
//                                    message:  phpErrorMsg.orderList[2]
//                                }
//                            }
//                },
        };

        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);
    </script>

</head>

<body>

    <div style="width:100%; margin:auto; " class="tab-panel-form">
        <div class="notification-msg"></div>

        <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
            <?php prepareOnLoadDataForm($obj); ?>
            <?php echo $obj->generateLangOptions(); ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('name', array('multilang' => true)); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'element' => array(
                                                'value' => 'categoryName',
                                                'key' => 'selCategory'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-diagnose.php',
                                                'data' => array('action' => 'searchData', 'isleaf' => 1)
                                            ) 
                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo  $obj->inputTextArea('trShortDesc', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
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