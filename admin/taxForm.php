<?php

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Tax.class.php', 'ChartOfAccount.class.php'));
$tax = new Tax();
$chartOfAccount = new ChartOfAccount();

$obj = $tax;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'taxList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsItemDescription = array();

$rs = prepareOnLoadData($obj);
$isSystemVariable = false;

if (!empty($_GET['id'])) { 
    if (!empty($rs[0]['taxincoakey'])) {
        $rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['taxincoakey']);
        $_POST['taxInCOAName'] = $rsCOAHeader[0]['code'] . ' - ' . $rsCOAHeader[0]['name'];
    }
    
    
    if (!empty($rs[0]['taxoutcoakey'])) {
        $rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['taxoutcoakey']);
        $_POST['taxOutCOAName'] = $rsCOAHeader[0]['code'] . ' - ' . $rsCOAHeader[0]['name'];
    }
    
    if($rs[0]['systemVariable'] == 1) $isSystemVariable = true;
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrType = $obj->generateComboboxOpt(array('data' => $obj->getTaxType()));
     
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        
      jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var LANG = '<?php echo json_encode(array('vatIn' => $obj->lang['vatIn'],
                              'vatOut' => $obj->lang['vatOut'] ,
                              'payableTax23' => $obj->lang['payableTax'] ,
                              'prepaidTax23' => $obj->lang['prepaidTax'] 
                              )) ?>';
          
        var tax = new Tax(tabID,LANG);
        
        prepareHandler(tax);   
        
         var fieldValidation =  {
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

                                        taxInCOAName: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.coa[1]
                                                }, 
                                            }
                                        },
                                        taxOutCOAName: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.coa[1]
                                                }, 
                                            }
                                        },
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
        
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
                                    <?php echo $obj->inputText('name', array('readonly' => $isSystemVariable)); ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selTaxType',$arrType, array('readonly' => $isSystemVariable)); ?>
                                </div>
                            </div>

                            <div class="form-group pph-type">
                                <label class="col-xs-3 control-label"><div class="label-in"></div></label>
                                <div class="col-xs-9">
                                    <?php 
                                    echo  $obj->inputAutoComplete(array( 
                                        'revalidateField' => true,
                                        'element' => array(
                                            'value' => 'taxInCOAName',
                                            'key' => 'hidTaxInCOAKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-coa.php',
                                            'data' => array('action' => 'searchData')
                                        )
                                    ));
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group  pph-type">
                                <label class="col-xs-3 control-label"><div class="label-out"></div></label>
                                <div class="col-xs-9">
                                    <?php 
                                    echo  $obj->inputAutoComplete(array( 
                                        'revalidateField' => true,
                                        'element' => array(
                                            'value' => 'taxOutCOAName',
                                            'key' => 'hidTaxOutCOAKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-coa.php',
                                            'data' => array('action' => 'searchData')
                                        )
                                    ));
                                    ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['order']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputNumber('orderList'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['witholdingReceipt']); ?></label>
                                <div class="col-xs-9">
                                  <?php echo $obj->inputCheckBox('chkHasWithholding'); ?>
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