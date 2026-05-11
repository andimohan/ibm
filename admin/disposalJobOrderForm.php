<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('DisposalJobOrder.class.php'));
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());
$customer = createObjAndAddToCol(new Customer()); 
$service = createObjAndAddToCol(new Service());
$disposalContract = createObjAndAddToCol(new DisposalContract());
$city = createObjAndAddToCol(new City());
$employee = createObjAndAddToCol(new Employee());
$waste = createObjAndAddToCol(new Waste());

$obj = $disposalJobOrder;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

// $overwriteContractAllowed = $security->isAdminLogin($disposalJobOrder->overwriteContractSecurityObject, 10);

$formAction = 'disposalJobOrderList';
$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$finalDiscDecimal = 0;

$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';


$rsPaymentMethodDetail = array();
$rsWasteDetail = array();
$finalDiscDecimalType = 'inputnumber';

$_POST['trDate'] = date('d / m / Y');
$lockTransactionDate = TABLENAME_SETTINGS[$obj->tableName]['locktransactiondate'];

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
    // kalo ad job baru dr case lama, oba kita ambil referensiny tetep dr case aj

    $id = $_GET['id']; 
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    $rsWasteDetail = $obj->getWasteDetail($id);
  
    $_POST['maximumWeight'] =  $obj->formatNumber($rs[0]['maximumweight'], 2); 
    $_POST['quotaServiced'] =  $obj->formatNumber($rs[0]['quotaserviced']); 

    if (!empty($rs[0]['contractkey'])) {
        $rsContract = $disposalContract->getDataRowById($rs[0]['contractkey']);
        $_POST['contractCode'] = $rsContract[0]['code'];
        $_POST['hidContractKey'] = $rsContract[0]['pkey'];
    }
    if (!empty($rs[0]['servicekey'])) {
        $rsService = $service->getDataRowById($rs[0]['servicekey']);
        $_POST['serviceName'] = $rsService[0]['name'];
        $_POST['hidServiceKey'] = $rsService[0]['pkey'];
    }

    if (!empty($rs[0]['citykey'])) {
        $_POST['hidAreaKey'] = $rs[0]['areakey'];
        $rsCity = $city->searchData($city->tableName . '.pkey', $rs[0]['citykey'], true);
        $_POST['area'] = $rsCity[0]['citycategoryname'];
        $_POST['hidCityKey'] = $rsCity[0]['pkey'];
    }

    if (!empty($rs[0]['customerkey'])) {
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }

    if (!empty($rs[0]['saleskey'])) {
        $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
        $_POST['hidSalesKey'] = $rsSales[0]['pkey'];
        $_POST['salesName'] = $rsSales[0]['name'];
    } 

    if (!empty($rs[0]['wastecategorykey'])) {
        $rsWasteCategory = $waste->getWasteCategory($rs[0]['wastecategorykey']);
        $_POST['hidWasteCategoryKey'] = $rsWasteCategory[0]['pkey'];
        $_POST['wasteCategoryName'] = $rsWasteCategory[0]['name'];
    }

    //update file  
    $obj->prepareLoadedFile($id, array('file' => $rsItemFile));
}
 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <script type="text/javascript">
        jQuery(document).ready(function(){

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var opt = Array();

            var disposalJobOrder = new DisposalJobOrder(tabID, opt);

            prepareHandler(disposalJobOrder);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                contractCode: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.contract[1]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('trDate',array('readonly' => $lockTransactionDate)); ?>
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['contract']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputAutoComplete(
                                        array( 
                                            'revalidateField' => true,
                                            'element' => array(
                                                'value' => 'contractCode',
                                                'key' => 'hidContractKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-disposal-contract.php',
                                                 'data' => array(  'action' =>'searchData' )
                                            ),
                                            'callbackFunction' => 'getTabObj().updateContract()'
                                        )
                                    );
                                    ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('salesName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidSalesKey'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidCustomerKey'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('area', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidCityKey'); ?>
                                    <?php echo $obj->inputHidden('hidAreaKey'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['service']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('serviceName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidServiceKey'); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['wasteCategory']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('wasteCategoryName', array('readonly' => true)); ?>
                                    <?php echo $obj->inputHidden('hidWasteCategoryKey'); ?>
                                    <?php echo $obj->inputHidden('hidServiceDetailWasteKey'); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['serviceInformation']); ?></div>
                              <div class="form-group">
                                <div class="col-xs-12">
                                <div class="flex">
                                    <div class="consume">
                                        <?php echo ucwords($obj->lang['duration']); ?> (<?php echo $obj->lang['month']; ?>)<br>
                                        <?php echo $obj->inputNumber('duration', array('etc' => 'style="text-align:right;padding-right:12px;"','readonly' => true)); ?></div>
                                    <div class="consume" style="padding:0 0.5em">
                                        <?php echo ucwords($obj->lang['totalVisit']); ?><br>
                                       <?php echo  $obj->inputNumber('qtyService', array('etc' => 'style="text-align:right;padding-right:12px;"','readonly' => true)); ?>
                                       </div>
                                    <!-- <div class="consume">
                                        <?php echo ucwords($obj->lang['maxWeight']); ?> (Kg)<br>
                                        <?php echo  $obj->inputDecimal('maximumWeight', array('etc' => 'style="text-align:right;padding-right:12px;"','readonly' => true)); ?>
                                    </div> -->
                                </div>
                                </div>
                            </div>
                              <div class="form-group" style="margin-top:1em">
                                <div class="col-xs-12">
                                <div class="flex">
                                    <div class="consume">
                                        <?php echo ucwords($obj->lang['sellingPrice']); ?><br>
                                        <?php echo $obj->inputNumber('sellingPrice', array('etc' => 'style="text-align:right;padding-right:12px;"','readonly' => true)); ?></div>
                                    <div class="consume" style="padding:0 0.5em">
                                        <?php echo ucwords($obj->lang['additional']); ?> / <?php echo ucwords($obj->lang['visit']); ?><br>
                                        <?php echo  $obj->inputNumber('exceedSellingPriceArea', array('etc' => 'style="text-align:right;padding-right:12px;"','readonly' => true)); ?>
                                    </div>
                                </div>
                                </div>
                            </div> 
                        </div> 

                        <div class="div-tab-panel">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['waste']); ?></div>

                                <div class="div-table mnv-transaction transaction-detail waste-detail package-detail" style="width:100%; border-bottom:1px solid #333; ">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['waste']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['sellingPrice']); ?> / kg</div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['minWeight']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['maxWeight']); ?></div>
                                    </div>

                                    <?php
                                    $totalRows = count($rsWasteDetail);

                                    for ($k = 0; $k <= $totalRows; $k++) {

                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $etc = '';
                                        $arrUnit = $arrDefaultUnit;

                                        if ($k == $totalRows) {
                                            $class = 'waste-row-template row-template';
                                            $overwrite = false;
                                            $etc = 'disabled="disabled"';
                                            $etc = '';
                                        } else {
                                            $decimal = 0;
                                            $inputnumber = 'inputnumber';

                                            $_POST['hidWasteDetailKey[]'] =  $rsWasteDetail[$k]['pkey'];
                                            $_POST['hidWasteKey[]'] =  $rsWasteDetail[$k]['wastekey'];
                                            $_POST['wasteName[]'] =  $rsWasteDetail[$k]['wastecodename'];
                                            $_POST['weightPrice[]'] =   $obj->formatNumber($rsWasteDetail[$k]['weightprice']);
                                            $_POST['minWeight[]'] =   $obj->formatNumber($rsWasteDetail[$k]['minweight'], 2);
                                            $_POST['maxWeight[]'] =   $obj->formatNumber($rsWasteDetail[$k]['maxweight'], 2);
                                        }

                                    ?>
                                        <div class="div-table-row <?php echo $class; ?>">
                                            <div class="div-table-col detail-col-detail">
                                                <?php echo $obj->inputText('wasteName[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                                                <?php echo $obj->inputHidden('hidWasteKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                                <?php echo $obj->inputHidden('hidWasteDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('weightPrice[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('minWeight[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('maxWeight[]', array('readonly' => true, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?></div>
                                        </div>
                                    <?php  }   ?>

                                </div>
                                <div style="clear:both; height:1em;"></div>

                            </div>
                        </div>
                              <div class="div-tab-panel">  
                                 <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['pickUpList']); ?></div> 
                                 <div class="div-table" style="width:100%"> 
                                      <div class="div-table-row"> 
                                         <div class="div-table-col-5" style="text-align:right; border-top:1px solid #666;border-bottom:1px solid #666; width:30px;" > 
                                            <strong><?php echo ucwords($obj->lang['number']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:120px;" > 
                                            <strong><?php echo ucwords($obj->lang['WOCode']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:120px;" > 
                                            <strong><?php echo ucwords($obj->lang['manifestCode']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:90px; text-align:center" > 
                                            <strong><?php echo ucwords($obj->lang['serviceWorkOrderDate']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right" > 
                                            <strong><?php echo ucwords($obj->lang['weight']); ?> (Kg)</strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right" > 
                                            <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right" > 
                                            <strong><?php echo ucwords($obj->lang['outstanding']); ?></strong>
                                         </div> 
                                     </div> 
                                     <?php 
                                        $no = 1;
                                        for ($i=0;$i<count($rsDetail);$i++){  
                                            
                                            $woCode = $rsDetail[$i]['wocode'];
                                            if ($rsDetail[$i]['wocode'] == $rsDetail[$i-1]['wocode']) {
                                                $woCode = '';
                                                $number = '';
                                            } else {
                                                $number = $no;
                                                $no++;
                                            }
                                            $manifestCode = $rsDetail[$i]['wastecode']. ' - ' .$rsDetail[$i]['manifestcode'];
                                            $woDate = $obj->formatDBDate($rsDetail[$i]['trdate'], 'd / m / Y');
                                            $disposalWeight = $obj->formatNumber($rsDetail[$i]['disposalweight'], 2);
                                            $amount = $obj->formatNumber($rsDetail[$i]['amount'], 2);
                                            $outstanding = $obj->formatNumber($rsDetail[$i]['amount'] - $rsDetail[$i]['totalinvoiced'], 2);
                      if ($outstanding < 0)  $outstanding = 0 ;

//                                            $statusDetail = $rsDetail[$i]['statusname'];
                                     ?>
                                             <div class="div-table-row">   
                                                    <div class="div-table-col-5" style="text-align:right; border-bottom:1px solid #dedede;"><?php echo $number; ?></div> 
                                                    <div class="div-table-col-5" style="border-bottom:1px solid #dedede;"><?php echo $woCode; ?></div>  
                                                    <div class="div-table-col-5" style="border-bottom:1px solid #dedede;"><?php echo $manifestCode; ?></div>  
                                                    <div class="div-table-col-5" style="text-align:center; border-bottom:1px solid #dedede;"><?php echo $woDate; ?></div> 
                                                    <div class="div-table-col-5" style="text-align:right; border-bottom:1px solid #dedede;"><?php echo $disposalWeight; ?></div>   
                                                    <div class="div-table-col-5" style="text-align:right; border-bottom:1px solid #dedede;"><?php echo $amount; ?></div>    
                                                    <div class="div-table-col-5" style="text-align:right; border-bottom:1px solid #dedede;"><?php echo $outstanding; ?></div>  
                                                </div> 
                                     <?php } ?> 
                                  </div>
                             </div>  
 
                    </div>
                </div>
            </div>
            <div style="clear:both"></div>
            <div class="form-button-margin"></div>
            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true);   ?>
            </div>
        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
