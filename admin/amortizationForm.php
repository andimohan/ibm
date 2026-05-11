<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Amortization.class.php'));
$amortization = createObjAndAddToCol(new Amortization());
$warehouse = new Warehouse();

$obj = $amortization;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true)) ;

$formAction = 'amortizationList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';

$rsDetail = array();


$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsDetail = $obj->getDetailWithRelatedInformation($id);
 
    $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>;

            var amortization = new Amortization(tabID);
            prepareHandler(amortization);
            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                }
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
                                <?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
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
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo $obj->inputTextArea('trNotes', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Detail Sini -->


            <div class="div-table mnv-transaction transaction-detail sales-delivery-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['prepaidExpense']); ?></div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemOrService']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:160px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
<!--                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>  icon-col" ></div> -->
                </div>
                
				<?php 
                            
                    $totalRows = count($rsDetail); 
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = '';
                        } else { 
                            $decimal = 0;
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidPrepaidExpenseKey[]'] = $rsDetail[$i]['refprepaidexpensekey']; 
                            $_POST['prepaidExpenseCode[]'] = $rsDetail[$i]['prepaidexpensecode']; 
                            $_POST['hidItemKey[]'] = $rsDetail[$i]['itemkey'];
                            $_POST['itemName[]'] = $rsDetail[$i]['servicename'];
                            $_POST['amount[]'] = $obj->formatNumber($rsDetail[$i]['amount']);  
                        }
                        
                    ?>    
        
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('prepaidExpenseCode[]',array('overwritePost' => $overwrite,  'readonly' => true, 'etc' => $etc)); ?>
                        <?php echo $obj->inputHidden('hidPrepaidExpenseKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
<!--                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>-->
                </div>
            
                <?php } ?> 
                   
            </div>                            
        

<!--
            <div style="clear:both; height:1em;"></div>
            <div style="float:left; display:inline-block;">
                <?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?>
            </div>
-->

            <div> 
                <div class="div-table" style="float:right;">
                   
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
<!--                        <div class="div-table-col <?php echo $obj->hideOnDisabled(); ?> icon-col"> </div>-->
                    </div> 
                     

                </div>   
                <div style="clear:both"></div>
            </div>

<!--
            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>
-->

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>