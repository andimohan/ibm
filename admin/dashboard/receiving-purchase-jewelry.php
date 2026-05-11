<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('ReceivingPurchaseJewelry.class.php'));

$receivingPurchaseJewelry = new ReceivingPurchaseJewelry();
$obj = $receivingPurchaseJewelry;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;


?>  
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive.css" /> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js"></script>
<!-- <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui-timepicker-addon.min.js"></script>  -->
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>moment.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.formatCurrency-1.4.0.min.js" ></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.111.min.js"></script>  

<title><?php echo $obj->lang['purchaseReceive']; ?></title> 
<style>
    .auto-complete .fa-sistrix {
        top: 0.6em;
    }

    .text-color-default {
        color: #555;
    }

    .text-color-negative {
        color: #c41e3a;
    }

    .text-color-positive {
        color: green;
    }
</style>
<script type="text/javascript">

    var objAndValueForDetailAutoComplete = [];

    objAndValue = new Array;
    objAndValue.push({object:'hidPackagingCodeKey[]', value :'pkey'});  
    objAndValue.push({object:'hidReceivingPurchaseKey[]', value :'reftransactionkey'});  
    objAndValue.push({object:'hidReceivingPurchaseDetailKey[]', value :'reftransactiondetailkey'});  
    objAndValue.push({object:'itemName[]', value :'itemname'});  
    objAndValue.push({object:'itemSKU[]', value :'itemcode'});  
    objAndValue.push({object:'qty[]', value :'qtyinbaseunit'});  
    objAndValue.push({object:'netWeight[]', value :'netweight'});  
    objAndValue.push({object:'grossWeight[]', value :'grossweight'});  
    objAndValueForDetailAutoComplete = objAndValue; 

    var purchaseDetailRow = 'transaction-detail-row';

    function rebindEl(){

        var receivingpurchasekey = $("[name=hidReceivingKey]").val();
        bindAutoCompleteForTransactionDetail('packagingCode[]',objAndValueForDetailAutoComplete,'../ajax-packaging-code.php?action=searchPackagingCodeForReceivingPurchase&receivingpurchasekey='+receivingpurchasekey+'&limit=25');   

        bindEl($("[name=btnAddDetailRow]"),'click', function() {
            addRowData($(this));
            calculateTotal();
        });

        $( "[name=btnDeleteRows]" ).on( "click", function() {  
            removeDetailRows(this); 
            calculateTotal();
        });


        $(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
        $(".input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),0); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),2); });
        $(".inputautodecimal").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),-2); });

        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this)); });

        bindEl($("[name='packagingCode[]']"),'change', function() { calculateTotal(); }); 

    }

    function afterSelectReceiving() {
        getTotalQty();
        rebindEl();
    }

    function parseJSON(data){ 

        data = $.trim(data);

        if(!data) data = '[]'; 
        if(data.length == 0) data = '[]'; 

        return JSON.parse(data);
    }

    function inputNumberOnBlur1(obj,decimal){  	
        if(obj.val() == "" || !$.isNumeric(unformatCurrency(obj.val())) )  
            obj.val(0);  
    
        obj.formatCurrency({roundToDecimalPlace: decimal });
    }

    function addRowData(row) { 
         var currentRow = $(row).closest(".transaction-detail-row");  
         newRow = $(".row-template").clone().removeClass("row-template").addClass(purchaseDetailRow); 
         newRow.insertAfter(currentRow);
         
         if (row == undefined) {
            $(".transaction-detail").append(newRow); 
         } else {
            newRow.insertAfter(currentRow);
         }

         rebindEl();
      }

    

    function getTotalQty()
    {
        var receivingkey = $("[name=hidReceivingKey]").val();

        if(receivingkey === "") {

            $("[name=qty]").val(0);
            $("[name=netWeight]").val(0);
            $("[name=grossWeight]").val(0);
                    
            $("[name=totalQtyHeader]").val(0);
            $("[name=totalNetWeightHeader]").val(0);
            $("[name=totalGrossWeightHeader]").val(0);

            $("[name=differenceQty]").val(0);
            $("[name=differenceGrossWeight]").val(0);
            $("[name=differenceNetWeight]").val(0);

            changeColorTotalDifference();

            return;
        }

        var ajaxData = "action=getTotalQty&pkey="+receivingkey;
        
        $.ajax({
           type: "GET",
            async: true,
            url: "ajax-receiving-purchase-jewelry.php",
            data: ajaxData, 
                beforeSend:function (xhr){    
                
                    $("[name=qty]").val(0);
                    $("[name=netWeight]").val(0);
                    $("[name=grossWeight]").val(0);
                    
                    $("[name=totalQtyHeader]").val(0);
                    $("[name=totalNetWeightHeader]").val(0);
                    $("[name=totalGrossWeightHeader]").val(0);

                    $("[name=barcode]").val("");

                    $(".transaction-detail-row").remove();
	            }, 
            success: function(res) {
                if(!res) return;

                var data = parseJSON(res); 

                $("[name=qty]").val(data[0].totalreceivedqtyinbaseunit).blur();
                $("[name=netWeight]").val(data[0].totalreceivedqtyinpcs).blur();
                $("[name=grossWeight]").val(data[0].totalgrossweight).blur();

                $("[name=totalQtyHeader]").val(data[0].totalreceivedqtyinbaseunit).blur();
                $("[name=totalNetWeightHeader]").val(data[0].totalreceivedqtyinpcs).blur();
                $("[name=totalGrossWeightHeader]").val(data[0].totalgrossweight).blur();

                addRowData();
                calculateTotal();
               
            }
         });

    }

    function getDataByBarcode()
    {
        var receivingkey = $("[name=hidReceivingKey]").val();
        var barcode = $("[name=barcode]").val();
  
        if(barcode == "") {
            alert('Barcode harus diisi.');
            return;
        }

        if(receivingkey.length <= 0) {
            $("[name=barcode]").val("");
            alert('Kode penerimaan harus diisi.');
            return;
        }

        var ajaxData = "action=getDataByBarcode&pkey="+receivingkey+"&barcode="+barcode;

        $.ajax({
            type: "GET",
            async: true,
            url: "../ajax-packaging-code.php",
            data: ajaxData, 
            beforeSend:function (xhr){    
                   
	        }, 
            success: function(res) {

                if(!res) return;

                var data = parseJSON(res); 

                if(data.length <= 0) {
                    alert("Barcode tidak ditemukan");
                    $("[name=barcode]").val("");
                    return;
                }

                var hasData = $(".transaction-detail-row").filter(function () {
                    var packagingCode = $(this).find("[name='packagingCode[]']").val() || "";
                    return packagingCode !== "";
                }).length > 0;

                
                if (!hasData) {
                    $(".transaction-detail-row").remove();
                }

                for(var i=0; i < data.length; i++) {
                    
                    var existingRow = $(".transaction-detail-row").filter(function () {
                        return $(this).find("[name='packagingCode[]']").val() === data[i].code;
                    }).first();
            
                    if (existingRow.length > 0) {
                        continue;
                    }
            
                    $newRow = $(".row-template").clone().removeClass("row-template").addClass(purchaseDetailRow); 
                    $newRow.insertBefore($(".row-template"));
                    
                    rebindEl();
    
                    updateRowValue($newRow,data[i]);  
                }

                $("[name=barcode]").val("");

            //    if (!data || data.length == 0) {
            //        addRowData();
            //    }
               
            }
         });

    }

    function updateRowValue(row, data) 
    {
        row.find("[name='hidPackagingKey[]']").val(data.pkey);
        row.find("[name='hidReceivingPurchaseKey[]']").val(data.reftransactionkey);
        row.find("[name='hidReceivingPurchaseDetailKey[]']").val(data.reftransactiondetailkey);
        row.find("[name='packagingCode[]']").val(data.code);
        row.find("[name='itemName[]']").val(data.itemname);
        row.find("[name='itemSKU[]']").val(data.itemcode);
        row.find("[name='qty[]']").val(data.qtyinbaseunit).blur();
        row.find("[name='netWeight[]']").val(data.netweight).blur();
        row.find("[name='grossWeight[]']").val(data.grossweight).blur();

        calculateTotal();

    }

    function calculateTotal()
    {
        var totalQty = 0
        var totalGrossWeight = 0
        var totalNetWeight = 0
        $("[name='qty[]']").each(function() {   
            detailRow = $(this).closest(".transaction-detail-row"); 
            totalQty += parseFloat(unformatCurrency($(this).val())) || 0;
            totalGrossWeight += parseFloat(unformatCurrency(detailRow.find("[name='grossWeight[]']").val())) || 0;
            totalNetWeight += parseFloat(unformatCurrency(detailRow.find("[name='netWeight[]']").val())) || 0;
        });
        
        $("[name=totalQty]").val(totalQty).blur()
        $("[name=totalGrossWeight]").val(totalGrossWeight).blur()
        $("[name=totalNetWeight]").val(totalNetWeight).blur()

        getTotalDifference();
    }

    function getTotalDifference() 
    {
        var differenceQty = 0;
        var differenceGrossWeight = 0;
        var differenceNetWeight = 0;

        var totalQty = $("[name=totalQty]").val();
        var totalGW = $("[name=totalGrossWeight]").val();
        var totalNW = $("[name=totalNetWeight]").val();

        var totalQtyHeader = $("[name=totalQtyHeader]").val();
        var totalGWHeader = $("[name=totalGrossWeightHeader]").val();
        var totalNWHeader = $("[name=totalNetWeightHeader]").val();

        differenceQty = totalQty - totalQtyHeader;
        differenceGrossWeight = totalGW - totalGWHeader;
        differenceNetWeight =  totalNW - totalNWHeader;


        $("[name=differenceQty]").val(differenceQty).blur();
        $("[name=differenceGrossWeight]").val(differenceGrossWeight).blur();
        $("[name=differenceNetWeight]").val(differenceNetWeight).blur();

        changeColorTotalDifference();

    }

    function changeColorTotalDifference() {
        const selectors = [
            "[name='differenceQty']",
            "[name='differenceGrossWeight']",
            "[name='differenceNetWeight']"
        ];

        selectors.forEach(selector => {
            const $el = $(selector);
            const value = parseFloat($el.val()) || 0;

            // Hapus semua class warna dulu
            $el.removeClass("text-color-negative text-color-positive text-color-default");

            if (value < 0) {
                $el.addClass("text-color-negative");
            } else if (value > 0) {
                $el.addClass("text-color-positive");
            } else {
                $el.addClass("text-color-default");
            }
        });
    }

    jQuery(document).ready(function(){

        bindEl($("[name=btnAddDetailRow]"),'click', function() {
            addRowData($(this));
        });

        $(".input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),0); });
        $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),2); });
        $(".inputautodecimal").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),-2); });
    
        $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this)); });
        $(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )

        $("[name=barcode]").on("keydown", function(e) {
            if (e.which === 13) {   
                e.preventDefault();                
                getDataByBarcode();
            }
        });

        addRowData();

    });

</script>

</head>
    <body> 
    <div class="dashboard">
        <div style="margin:2em">    
		
	        <h1><?php echo strtoupper($obj->lang['purchaseReceive']); ?></h1>
		
            <div style="width:100%; margin:auto; " class="tab-panel-form"></div>
            <div class="notification-msg"></div>

            <form id="receiving-purchase-jewelry">

                <div style="display:flex; align-items:center; flex-wrap:wrap; gap:0.5em;">
                
                    <div style="width:100px">
                        <label for="receivingCode">Kode Penerimaan  </label>
                    </div>
                    <div style="width:250px">
                            <?php echo $obj->inputAutoComplete(array(
                                        'revalidateField' => false,
                                        'element' => array(
                                            'value' => 'receivingCode',
                                            'key' => 'hidReceivingKey'
                                        ),
                                        'source' => array(
                                            'url' => 'ajax-receiving-purchase-jewelry.php',
                                            'data' => array('action' => 'searchData', 'statuskey' => '(2,3)')
                                        ),
                                        'callbackFunction' => 'afterSelectReceiving()',
                                        'placeholder' => $obj->lang['purchaseReceive']
                                    ));
                            ?>
                    </div>

                    <div style="width:50px"></div>

                    <div style="width:50px;text-align:right;">
                        <label for="receivingCode">Qty</label>
                    </div>
                    <div style="width:120px;text-align:right;">
                        <?php echo $obj->inputNumber('qty', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                    </div>

                    <div style="width:50px;text-align:right;">
                        <label for="receivingCode">NW</label>
                    </div>
                    <div style="width:120px;text-align:right;">
                        <?php echo $obj->inputDecimal('netWeight', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                    </div>

                    <div style="width:50px;text-align:right;">
                        <label for="receivingCode">GW</label>
                    </div>
                    <div style="width:120px;text-align:right;">
                        <?php echo $obj->inputDecimal('grossWeight', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                    </div>

                </div>

                 <div style="clear:both; height: 1em"></div> 

                <div style="width:215px">
                    <?php echo $obj->inputText('barcode', array('etc' => 'placeholder="Input Barcode Packaging"')); ?>
                </div>

                <div style="clear:both; height: 1em"></div> 

                <div style="width: 100%; overflow:auto">
                    <div class="div-table transaction-detail" style="  border-bottom:1px solid #333; width: 100em "> 
                        <div class="div-table-row  odd-style-adjustment odd-white"> 
                            <div class="div-table-col detail-col-header" style="position:sticky; left: 0em;background-color:#fff;"><?php echo ucwords('No. Packaging'); ?></div>
                            <div class="div-table-col detail-col-header" style="width:16em;text-aling:left;"><?php echo ucwords('Nama Barang'); ?></div>
                            <div class="div-table-col detail-col-header" style="width:13em;text-aling:left;"><?php echo ucwords('SKU'); ?></div>
                            <div class="div-table-col detail-col-header" style="width:9em;text-align:right;"><?php echo ucwords('QTY'); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:9em;text-align:right;"><?php echo ucwords('NW'); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:9em;text-align:right;"><?php echo ucwords('GW'); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:4em;"></div>
                        </div>


                        <div class="div-table-row row-template transaction-row   odd-style-adjustment odd-white">
                            <div class="div-table-col detail-col-detail" style="position:sticky; left: 0em">
                                <?php echo $obj->inputText('packagingCode[]', array('overwritePost' => true, 'class' => 'form-control', 'etc' => 'style="text-align:left"' )); ?>
                                <?php echo $obj->inputHidden('hidPackagingKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
                                <?php echo $obj->inputHidden('hidReceivingPurchaseDetailKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
                                <?php echo $obj->inputHidden('hidReceivingPurchaseKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
                            </div>
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputText('itemName[]', array('overwritePost' => true,  'readonly' => true, 'class' => 'form-control', 'etc' => 'style="text-align:left"')); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputText('itemSKU[]', array('overwritePost' => true, 'readonly'  => true, 'class' => 'form-control', 'etc' => 'style="text-align:left"')); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputNumber('qty[]', array('overwritePost' => true, 'readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputDecimal('netWeight[]', array('overwritePost' => true, 'readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputDecimal('grossWeight[]', array('overwritePost' => true, 'readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?>
                            </div> 

                            <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>">
                                <div class="flex">
                                    <div><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button')); ?></div>
                                    <div><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0;"')); ?></div>
                                </div>
                            </div> 

                        </div>

                    </div>

                    
                    <div style="clear:both; height:0.5em;"></div>  
                    
                    <div style="text-align:right;width: 100em">
                        <div class="div-table" style="display:inline-block;">
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputNumber('totalQty',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputDecimal('totalNetWeight',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputDecimal('totalGrossWeight',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-header" style="width:5.5em;"></div>
                        </div>
                    </div>
                    <div style="text-align:right;width: 100em">
                        <div class="div-table" style="display:inline-block;">
                            <div class="div-table-col detail-col-detail"  style="width:13em; text-align:left; padding:0 4px"><?php echo ucwords($obj->lang['purchaseReceive']); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputNumber('totalQtyHeader',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputDecimal('totalNetWeightHeader',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:0 4px"><?php echo $obj->inputDecimal('totalGrossWeightHeader',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-header" style="width:5.5em;"></div>
                        </div>
                    </div>
                    <div style="text-align:right;width: 100em">
                        <div class="div-table" style="display:inline-block;">
                            <div class="div-table-col detail-col-detail"  style="width:13em; text-align:left; padding:4px 4px;border-top:1px solid #333;"><?php echo ucwords('Selisih'); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:4px 4px;border-top:1px solid #333;"><?php echo $obj->inputNumber('differenceQty',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:4px 4px;border-top:1px solid #333;"><?php echo $obj->inputDecimal('differenceNetWeight',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-detail"  style="width:9em; vertical-align:top; padding:4px 4px;border-top:1px solid #333;"><?php echo $obj->inputDecimal('differenceGrossWeight',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                            <div class="div-table-col detail-col-header" style="width:5.5em;"></div>
                        </div>
                    </div>
                </div>  
                <div class="div-table transaction-row" style="width:100%;">
                </div> 

            </form>
            
        </div>
	</div> 
    </body>    

</html>