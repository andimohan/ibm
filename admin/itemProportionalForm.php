<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('ItemProportional.class.php');
$itemProportional = createObjAndAddToCol(new ItemProportional());
$item = createObjAndAddToCol( new Item()); 
$coa = createObjAndAddToCol( new ChartOfAccount()); 

$obj = $itemProportional;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$formAction = 'itemProportionalList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);

$rsDetailOwner = array();
$rsDetailTenant = array();

$_POST['itemDateHomeOwner[]'] = date('d / m / Y');
$_POST['itemDateHouseTenant[]'] = date('d / m / Y');


if (!empty($_GET['id'])) {
    $id = $_GET['id'];
     
    $rsDetail = $obj->getDetailItemPercentage($id);

    
	$rsItem = $item->getDataRowById($rs[0]['itemkey']);
    $_POST['hidItemKey'] = $rs[0]['itemkey'];
    $_POST['itemName'] = $rsItem[0]['name'];

    
    if(!empty($rs[0]['coakey'])){
        $rsCOA = $coa->getDataRowById($rs[0]['coakey']);
        $_POST['hidCOAKey'] = $rs[0]['coakey'];
        $_POST['coaName'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'];

    }
    
    $_POST['remainPercentage'] = $obj->formatNumber($rs[0]['remainpercentage']);
   

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">

        jQuery(document).ready(function () {

            var tabID = selectedTab.newPanel[0].id;
            var itemProportional = new ItemProportional(tabID);

            prepareHandler(itemProportional);

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
                itmeName: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.item[1]
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
                            <div class="div-table-caption border-orange">
                                <?php echo ucwords($obj->lang['generalInformation']); ?>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['status']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['code']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">
                                    <?php echo $obj->lang['name']; ?>
                                </label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                            <?php echo $obj->inputText('name'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['item']; ?></label>  
                                <div class="col-xs-9"> 
                                        <?php    
                                        echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $item,
                                                                            'revalidateField' => true,
                                                                            'element' => array('value' => 'itemName',
                                                                                                'key' => 'hidItemKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-item.php',
                                                                                                'data' => array( 'itemtype' => 1,  'action' =>'searchData' )
                                                                                            ) 
                                                                            )
                                                                    );  
                                        ?> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['wasteAccount']; ?></label>  
                                <div class="col-xs-9"> 
                                        <?php    
                                        echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $coa,
                                                                            'revalidateField' => true,
                                                                            'element' => array('value' => 'coaName',
                                                                                                'key' => 'hidCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            ) 
                                                                            )
                                                                    );  
                                        ?> 
                                </div> 
                            </div>  
                            

                            <div class="form-group" style="display:none">
                                <label class="col-xs-3 control-label">
                                    <?php echo ucwords($obj->lang['remainPercentage']); ?>
                                </label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputNumber('remainPercentage', array('etc' => 'readonly="readonly"')); ?></div>
                                        <div>%</div>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                          <div class="div-tab-panel">
                                <div class="div-table-caption border-blue">
                                    <?php echo ucwords($obj->lang['itemPercentage']); ?>
                                </div>
                                <div class="div-table mnv-transaction transaction-detail" style="width:100%;  ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"  style="border:0">
                                            <?php echo ucwords($obj->lang['item']); ?>
                                        </div>
                                        <div class="div-table-col detail-col-header" style="text-align:left;border:0">
                                            <?php echo ucwords($obj->lang['itemPercentage']) ?>
                                        </div>
                                        <div class="div-table-col detail-col-header" style="text-align:left;border:0"> </div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"style="width:45px; border:0"></div>
                                    </div>

                                    <?php

                                    $totalRows = count($rsDetail);

                                    for ($i = 0; $i <= $totalRows; $i++) {

                                        $class = 'transaction-detail-row';
                                        $overwrite = true;
                                        $disabled = false;
                                        $optionRows = 'display:none';
                                        $totalDetailRows = 0;

                                        if ($i == $totalRows) {
                                            $class = 'detail-row-template ';
                                            $overwrite = false;
                                            $disabled = true;

                                        } else {
                                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                                            $_POST['hidItemDetailKey[]'] = $rsDetail[$i]['itemkey'];
                                            $_POST['itemDetailName[]'] = $rsDetail[$i]['itemname'];
                                            $_POST['detailPercentage[]'] =  $obj->formatNumber($rsDetail[$i]['percentage']);

                                        }

                                        ?>

                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail">
                                               <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                <?php echo $obj->inputText('itemDetailName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            </div>
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputNumber('detailPercentage[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" '.$etc)); ?>
                                            </div> 
                                             <div class="div-table-col detail-col-detail">%</div> 
                                            <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>">
                                                <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?>
                                            </div>
                                        </div>

                                    <?php } ?>


                                </div>

                                <div style="clear:both; height:1em;"></div>
                                <div style="float:left; display:inline-block;">
                                    <?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                </div>

                            </div>
                            
                    </div>
                    
                    <div class="div-table-col">
                        <div class="div-tab-panel">

                            <div class="div-table-caption border-purple">
                                <?php echo ucwords($obj->lang['note']); ?>
                            </div>
                            <?php echo $obj->inputTextArea('trDesc', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
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
