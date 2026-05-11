<?php
require_once '../../../_config.php';
require_once '../../../_include-v2.php';


includeClass(array('EMKLHouseBL.class.php'));
$emklHBL = createObjAndAddToCol(new EMKLHouseBL());
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$consignee = createObjAndAddToCol(new Consignee());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$itemUnit = createObjAndAddToCol(new ItemUnit());

$obj = $emklHBL;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
    ;

$formAction = 'emklHouseBLList';


$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');


$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $rsJO = $emklJobOrder->searchDataForInvoice($emklJobOrder->tableNameDetail . '.pkey', $rs[0]['refkey'], true);

    $_POST['hidJobOrderKey'] = $rsJO[0]['pkey'];
    $_POST['jobOrderCode'] = $rsJO[0]['value'];

    if (!empty($rs[0]['shipperkey'])) {
        $rsShipper = $customer->getDataRowById($rs[0]['shipperkey']);
        $_POST['shipperName'] = $rsShipper[0]['name'];
        $_POST['shipperAddress'] = $rsShipper[0]['address'];
    }

    if (!empty($rs[0]['consigneekey'])) {
        $rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
        $_POST['consigneeName'] = $rsConsignee[0]['name'];
        $_POST['consigneeAddress'] = $rsConsignee[0]['address'];
    }

    if (!empty($rs[0]['carrierkey'])) {
        $rsCarrier = $consignee->getDataRowById($rs[0]['carrierkey']);
        $_POST['carrierName'] = $rsCarrier[0]['name'];
    }

    if (!empty($rs[0]['podkey'])) {
        $rsPOD = $port->getDataRowById($rs[0]['podkey']);
        $_POST['podName'] = $rsPOD[0]['name'];
    }

    if (!empty($rs[0]['podeliverykey'])) {
        $rsPODelivery = $port->getDataRowById($rs[0]['podeliverykey']);
        $_POST['placeOfDeliveryName'] = $rsPODelivery[0]['name'];
    }

    if (!empty($rs[0]['poreceiptkey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['poreceiptkey']); 
        $_POST['placeOfReceiptName'] = $rsPODelivery[0]['name'];
    }

    if (!empty($rs[0]['polkey'])) {
        $rsPOL = $port->getDataRowById($rs[0]['polkey']);
        $_POST['polName'] = $rsPOL[0]['name'];
    }
    if (!empty($rs[0]['agentkey'])) {
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']);
        $_POST['agentName'] = $rsAgent[0]['name'];
    }

    $_POST['chkIsOverwriteShipper'] = $rs[0]['isoverwriteshipper'];
    $_POST['chkIsOverwriteConsignee'] = $rs[0]['isoverwriteconsignee'];
    


    $_POST['note'] = $rs[0]['note'];
    $_POST['weight'] = $obj->formatNumber($rs[0]['weight'], 2);
    $_POST['volume'] = $obj->formatNumber($rs[0]['volume'], 3);
    $_POST['qty'] = $obj->formatNumber($rs[0]['qty']);
    $_POST['selUnit'] = $rs[0]['unitkey'];

    $_POST['selShipmentTermKey'] = $rs[0]['shipmenttermkey'];
    $_POST['selShipmentTerm2Key'] = $rs[0]['shipmentterm2key'];

    $_POST['numberOfOriginal'] = $rs[0]['numberoforiginal'];

}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrUnit = $class->convertForCombobox($itemUnit->searchData('', '', true, ' and (' . $itemUnit->tableName . '.statuskey = 1 )'), 'pkey', 'name');
$arrShipmentTerm = $obj->generateComboboxOpt(array('data' => $emklJobOrder->getShipmentTerm()));

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


            var emklHouseBL = new EMKLHouseBL(tabID);
            prepareHandler(emklHouseBL);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
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
                                <?php echo ucwords($obj->lang['generalInformation']); ?></div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selStatus', $arrStatus); ?>
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
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $emklJobOrder,
                                            'element' => array(
                                                'value' => 'jobOrderCode',
                                                'key' => 'hidJobOrderKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-emkl-job-order.php',
                                                'data' => array('action' => 'searchDataForInvoice')
                                            ),
                                            'callbackFunction' => 'getTabObj().updateFromJobOrder()'

                                        )
                                    );
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <div class="non-overwrite-shipper">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $customer,
                                                        'element' => array(
                                                            'value' => 'shipperName',
                                                            'key' => 'hidShipperKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-customer.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'callbackFunction' => 'getTabObj().updateShipper()'

                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="overwrite-shipper">
                                                <?php echo $obj->inputText('shipperName1'); ?></div>
                                        </div>

                                        <div style="padding-left: 0.5em">
                                            <div style="float:left; margin-top:0.1em" rel="shipper">
                                                <?php echo $obj->inputCheckBox('chkIsOverwriteShipper'); ?></div>
                                            <div style="float:left; margin-left:0.5em">
                                                <?php echo ucwords($obj->lang['overwrite']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"></label>
                                <div class="col-xs-9">
                                    <div class="non-overwrite-shipper">
                                        <?php echo $obj->inputTextArea('shipperAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?>
                                    </div>
                                    <div class="overwrite-shipper">
                                        <?php echo $obj->inputTextArea('shipperAddress1', array('etc' => 'style="height:10em;"')); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <div class="non-overwrite-consignee">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $consignee,
                                                        'element' => array(
                                                            'value' => 'consigneeName',
                                                            'key' => 'hidConsigneeKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-consignee.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'callbackFunction' => 'getTabObj().updateConsignee()'

                                                    )
                                                );
                                                ?>
                                            </div>
                                            <div class="overwrite-consignee">
                                                <?php echo $obj->inputText('consigneeName1'); ?></div>
                                        </div>
                                        <div style="padding-left: 0.5em">
                                            <div style="float:left; margin-top:0.1em" rel="consignee">
                                                <?php echo $obj->inputCheckBox('chkIsOverwriteConsignee'); ?></div>
                                            <div style="float:left; margin-left:0.5em">
                                                <?php echo ucwords($obj->lang['overwrite']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-xs-3 control-label"></label>
                                <div class="col-xs-9">
                                    <div>
                                        <div class="non-overwrite-consignee">
                                            <?php echo $obj->inputTextArea('consigneeAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?>
                                        </div>
                                        <div class="overwrite-consignee">
                                            <?php echo $obj->inputTextArea('consigneeAddress1', array('etc' => 'style="height:10em;"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $consignee,
                                            'element' => array(
                                                'value' => 'carrierName',
                                                'key' => 'hidCarrierKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-consignee.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <div class="non-overwrite-carrier">
                                                    <?php
                                                        echo $obj->inputAutoComplete(
                                                            array(
                                                                'objRefer' => $consignee,
                                                                'element' => array(
                                                                    'value' => 'carrierName',
                                                                    'key' => 'hidCarrierKey'
                                                                ),
                                                                'source' => array(
                                                                    'url' => 'ajax-consignee.php',
                                                                    'data' => array('action' => 'searchData')
                                                                ),
                                                                'callbackFunction' => 'getTabObj().updateNotifyParty()'
                                                            )
                                                        );
                                                    ?>
                                                </div>
                                                <div class="overwrite-carrier"><?php echo $obj->inputText('carrierName1'); ?></div>
                                            </div>
                                            <div style="padding-left: 0.5em">
                                                <div style="float:left; margin-top:0.1em" rel="carrier">
                                                    <?php echo $obj->inputCheckBox('chkIsOverwriteCarrier'); ?>
                                                </div>
                                                <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="form-group ">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9"> 
                                    <div>
										<div class="non-overwrite-carrier"><?php echo $obj->inputTextArea('carrierAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?></div>
                                        <div class="overwrite-carrier">
                                            <?php echo $obj->inputTextArea('carrierAddress1', array('etc' => 'style="height:10em;"')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label">POL / POD</label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $port,
                                                    'element' => array(
                                                        'value' => 'polName',
                                                        'key' => 'hidPOLKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-port.php',
                                                        'data' => array('action' => 'searchData')
                                                    )
                                                )
                                            );
                                            ?>
                                        </div>
                                        <div>/</div>
                                        <div class="consume">
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $port,
                                                    'element' => array(
                                                        'value' => 'podName',
                                                        'key' => 'hidPODKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-port.php',
                                                        'data' => array('action' => 'searchData')
                                                    )
                                                )
                                            );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="form-group">
                                <label class="col-xs-3 control-label">POL / <?php echo ucwords($obj->lang['placeOfReceipt']); ?></label> 
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"> 
                                            <div class="overwrite-POL"><?php echo $obj->inputText('portOfLoading'); ?></div>
                                            <div class="non-overwrite-POL">
                                                <?php  echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port, 
                                                                                'element' => array('value' => 'polName',
                                                                                                'key' => 'hidPOLKey'),
                                                                                'source' =>array(
                                                                                                'url' => 'ajax-port.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                                )
                                                                            );  
                                                ?>
                                            </div>
                                        </div> 
                                        <div>/</div>
                                            <div class="consume"> 
                                                <div class="overwrite-POD"><?php echo $obj->inputText('placeOfReceipt'); ?></div>
                                                <div class="non-overwrite-POD">
                                                    <?php  echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $port,
                                                                            'revalidateField' => false,  
                                                                            'element' => array('value' => 'placeOfReceiptName',
                                                                                                'key' => 'hidPOReceiptKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-port.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ),
                                                                            'allowedStatusForEdit' => array (1),
                                                                            'etc' => $attrHeader   
                                                                    )
                                                                );  
                                                    ?>   
                                                </div> 
                                            </div>  
                                            <div style="padding-left: 0.5em">
                                                <div style="float:left; margin-top:0.1em" rel="POL">
                                                    <?php echo $obj->inputCheckBox('chkIsOverwritePOL'); ?>
                                                </div>
                                                <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                            </div>
                                    </div> 
                                </div> 
                            </div>

                            <!-- <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeOfDelivery']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $port,
                                            'element' => array(
                                                'value' => 'placeOfDeliveryName',
                                                'key' => 'hidPODeliveryKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-port.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div> -->
                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeOfDelivery']); ?> / <?php echo ucwords($obj->lang['placeOfReceipt']); ?></label> 
                                        <div class="col-xs-9">
                                        <div class="flex">
                                                <div class="consume"> 
                                                    
                                                        <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                                                        'element' => array('value' => 'placeOfDeliveryName',
                                                                                                        'key' => 'hidPODeliveryKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ),
                                                                                        'allowedStatusForEdit' => array (1),
                                                                                        'etc' => $attrHeader   
                                                                                    )
                                                                                );  
                                                        ?>  
                                                </div> 
                                                <div>/</div>
                                                    <div class="consume"> 
                                                        
                                                            <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                                                        'element' => array('value' => 'placeOfReceiptName',
                                                                                                        'key' => 'hidPOReceiptKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ),
                                                                                        'allowedStatusForEdit' => array (1),
                                                                                        'etc' => $attrHeader   
                                                                                    )
                                                                                );  
                                                            ?>   
                                                        
                                                    </div> 
                                                </div> 
                                        </div> 
                                    </div> -->


                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">POD / <?php echo ucwords($obj->lang['placeOfDelivery']); ?></label> 
                                        <div class="col-xs-9">
                                        <div class="flex">
                                            
                                                <div class="consume"> 
                                                    <div class="overwrite-POL"><?php echo $obj->inputText('portOfDischarge'); ?></div>
                                                    <div class="non-overwrite-POL">
                                                    <?php  echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $port, 
                                                                                            'element' => array('value' => 'podName',
                                                                                                            'key' => 'hidPODKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-port.php',
                                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                                            ) 
                                                                                        )
                                                                                    );  
                                                        ?>
                                                    </div>
                                                </div> 
                                                <div>/</div>
                                                <div class="consume"> 
                                                    <div class="overwrite-POD"><?php echo $obj->inputText('placeOfDelivery'); ?></div>
                                                    <div class="non-overwrite-POD">
                                                        <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                                                        'element' => array('value' => 'placeOfDeliveryName',
                                                                                                        'key' => 'hidPODeliveryKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ),
                                                                                        'allowedStatusForEdit' => array (1),
                                                                                        'etc' => $attrHeader   
                                                                                    )
                                                                                );  
                                                        ?>  
                                                    </div>   
                                                </div> 
                                                    
                                                <div style="padding-left: 0.5em">
                                                    <div style="float:left; margin-top:0.1em" rel="POD">
                                                        <?php echo $obj->inputCheckBox('chkIsOverwritePOD'); ?>
                                                    </div>
                                                    <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                                </div>
                                                </div> 
                                        </div> 
                                    </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label>
                                <div class="col-xs-9">
                                    <?php
                                    echo $obj->inputAutoComplete(
                                        array(
                                            'objRefer' => $customer,
                                            'element' => array(
                                                'value' => 'agentName',
                                                'key' => 'hidAgentKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array('action' => 'searchData')
                                            )
                                        )
                                    );
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['exportReference']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('exportReference'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['merchant']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('merchant'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipmentTerm']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputSelect('selShipmentTermKey', $arrShipmentTerm); ?>
                                        </div>
                                        <div>-</div>
                                        <div class="consume">
                                            <?php echo $obj->inputSelect('selShipmentTerm2Key', $arrShipmentTerm); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['freightCharges']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('freightCharges'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                        <label class="col-xs-3 control-label">Number Of Original B/L</label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputNumber('numberOfOriginal'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['telex']); ?></label>
                                <div class="col-xs-9  control-label">
                                    <div class="flex">
                                        <div><?php echo $obj->inputCheckBox('chkIsRelease'); ?></div>
                                        <div><?php echo $obj->lang['release']; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('note', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="div-table-col">
                        <div class="div-tab-panel">
                            <div class="div-table-caption border-green">
                                <?php echo ucwords($obj->lang['itemPackage'] . ' & ' . $obj->lang['goodsDescription']); ?>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemPackage']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('package'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['weight']); ?> /
                                    <?php echo ucwords($obj->lang['volume']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex">
                                        <div><?php echo $obj->inputNumber('qty'); ?></div>
                                        <div style="width:30%" style="margin-right:10px">
                                            <?php echo $obj->inputSelect('selUnit', $arrUnit, array('add-class' => 'label-style')); ?>
                                        </div>
                                        <div><?php echo $obj->inputDecimal('weight'); ?></div>
                                        <div class="text-muted" style="margin-right:10px">KG</div>
                                        <div>
                                            <?php echo $obj->inputDecimal('volume', array('etc' => 'mnv-attr-decimal="3"')); ?>
                                        </div>
                                        <div class="text-muted" style="margin-right:10px">CBM</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['marksAndNumber']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('marksNumber', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['goodsDescription']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('shortDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label
                                    class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attachment']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label
                                    class="col-xs-3 control-label"><?php echo $obj->lang['containerInformationInWords']; ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputTextArea('sayTotalContainer', array('etc' => 'style="height:10em;"')); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(array(), true); ?>
            </div>

        </form>

        <?php echo $obj->showDataHistory(); ?>
    </div>
</body>

</html>
