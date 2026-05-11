<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $truckingPurchaseOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$overwriteContractAllowed = $security->isAdminLogin($truckingServiceOrder->overwriteContractSecurityObject,10);

$formAction = 'truckingPurchaseOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
   

$rsDetail = array();
//$arrContract = array();

$defaultShipmentDate = date('d / m / Y 00:00');
$_POST['trDate'] = date('d / m / Y'); 
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

//$rsCost = $cost->searchData($cost->tableName.'.statuskey',1, true, '','order by fixedcost desc, name asc');  
  
// get status color
$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');
    
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
          
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
	//$_POST['selContract'] = $rs[0]['contractkey'];   
   
    

	if (!empty($rs[0]['sokey'])){
        $_POST['hidSOKey'] = $rs[0]['sokey'] ;
        $rsTrucking = $truckingServiceOrder->getDataRowById($rs[0]['sokey']);
        $_POST['soNumber'] = $rsTrucking[0]['code'] ;
    } 
    
    if (!empty($rs[0]['supplierkey'])){
        $_POST['hidSupplierKey'] = $rs[0]['supplierkey'] ;  
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']); 
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }
    
	 
	
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']); 
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ; 
    
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	   
 
}
$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name');
$arrPaymentMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'); 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
    var cashTOP = Array();
    var firstOpened = true; 
    
    <?php 
        for ($i=0;$i<count($rsTOP);$i++){
            if ($rsTOP[$i]['duedays'] <> 0)
            echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
		  }
	 ?> 
	
    function TruckingPurchaseOrder(tabID) {  
        
         this.updateDetail = function updateDetail(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
				  
                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);   
                truckingPurchaseOrder.updateDetailInformation (detailRow); 

         }
         
         this.updateAllDetailInformation = function updateAllDetailInformation(){
             $(".transaction-detail-row").each(function(){   
                  truckingPurchaseOrder.updateDetailInformation($(this));
             })    
         }
         
         this.updateDetailInformation = function updateDetailInformation(detailRow){ 
             
                //var contractkey = $("#" + tabID + " [name=selContract]").val();
                var contractkey = $("#" + tabID + " [name=hidContractKey]").val();
                var itemkey = detailRow.find("[name=\"hidItemKey[]\"]").val();
              
                var obj = detailRow.find("[name=\"statusName[]\"]");
                $(obj).addClass("text-white bg-green-avocado").val(status);
                
                //update price
                 $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-selling-rate.php',
                    async: false,
                    data: "action=getDetail&contractkey=" + contractkey + "&itemkey=" + itemkey ,  
                }).done(function( data ) { 
                    
                    if(data.length == 0)
                        return;
                     
                    data = JSON.parse(data) ; 
                     
                    if (data.length > 0){  
                        data = data[0];   
                        price = data.price; 
                         
                        //truckingPurchaseOrder.updateCostInformation(detailRow); 
                        
                    }else{
                        price = 0;
                    }
                     
                    detailRow.find("[name=\"price[]\"]").val(price).blur().change(); 
                    detailRow.find("[name=\"statusName[]\"]").val("Open");  
                });  
                
         } 
            
        

        
        
              
 
    
	this.calculateDetail = function calculateDetail(obj){     
        var parentObj =  $(obj).closest(".transaction-detail-row");

        var itemkey =  parentObj.find("[name='hidItemKey[]']").val(); 
        if (itemkey == undefined)
            return;

        var qty =  unformatCurrency(parentObj.find("[name='qty[]']").val());
        var priceInUnit =  unformatCurrency(parentObj.find("[name='priceInUnit[]']").val()); 
  
        var subtotal = qty * priceInUnit; 
        parentObj.find("[name='amount[]']").val(subtotal).blur(); 
 
        truckingPurchaseOrder.calculateTotalSales();
    }
	
	this.calculateTotalSales = function calculateTotalSales(){  
            var subtotal = 0; 
            $("#" + tabID + " [name='amount[]']").each(function() {    
                    subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
            }) 
            $("#" + tabID + " [name='subtotal']").val(subtotal).blur();
         
         
            truckingPurchaseOrder.calculateTotal();
            truckingPurchaseOrder.recountNumber();
	 }
    
     
	
    
    
    
	this.calculateDetailCost = function calculateDetailCost(obj){     
        var parentObj =  $(obj).closest(".transaction-detail-row");

        var itemkey =  parentObj.find("[name='hidItemKeyCost[]']").val(); 
        if (itemkey == undefined)
            return;

        var qty =  unformatCurrency(parentObj.find("[name='qtyCost[]']").val());
        var priceInUnit =  unformatCurrency(parentObj.find("[name='priceCost[]']").val()); 
  
        var subtotal = qty * priceInUnit; 
        parentObj.find("[name='subtotalCost[]']").val(subtotal).blur();   
 
        truckingPurchaseOrder.calculateTotalCost();
    }
     
	
	this.calculateTotalCost = function calculateTotalCost(){  
            var subtotal = 0; 
            $("#" + tabID + " [name='subtotalCost[]']").each(function() {    
                    subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
            })

            $("#" + tabID + " [name='totalCost']").val(subtotal).blur();
         
            truckingPurchaseOrder.calculateTotal(); 
            truckingPurchaseOrder.calculateCostSummary(); 
	 }
    
    
	this.calculateTotal = function calculateTotal(){  
        var subtotal =   parseInt(unformatCurrency($("#" + tabID + " [name='subtotal']").val()));
        
        var includeTax =   $("#" + tabID + " [name='chkIncludeTax']").val();
        var taxPercentage =  parseInt(unformatCurrency($("#" + tabID + " [name='taxPercentage']").val())) || 0 ;
        
        var taxValue = 0;
            if (includeTax == 0) {
                taxValue = subtotal * taxPercentage / 100;
                subtotal += taxValue;
            }else{
                taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                subtotal -= taxValue; 
            }

        $("#" + tabID + " [name='taxValue']").val(taxValue).blur();
        
        
        var total = subtotal; 
        $("#" + tabID + " [name='total']").val(total).blur(); 
        
        var totalPayment = 0; 
        $("#" + tabID + " [name='paymentMethodValue[]']").each(function() {   
            totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
        }) 

        var balance = totalPayment - total;
        $("#" + tabID + " [name='balance']").val(balance).blur();
    }
    
    this.onChangeCustomer = function onChangeCustomer(){
         $obj = $("#" + tabID + " name=[categoryName]");
         $obj.closest('form').bootstrapValidator('revalidateField', $obj.attr("name"));
    }
    
        
     this.recountNumber = function recountNumber(){ 
            // recount row number_format
            var ctr = 1;
            $("#" + tabID + " .row-number").each(function() { $(this).html(ctr++ + '.'); })   
     } 
     
     this.updateTOP = function updateTOP(){
          
            var selTermOfPaymentKey = $( "#" + tabID + " [name=selTermOfPaymentKey]" ).val();   
            var supplierkey = $( "#" + tabID + " [name=hidSupplierKey]" ).val(); 

            $.ajax({
                    type: "GET",
                    url:  'ajax-supplier.php',
                    data: "action=getDataRowById&pkey=" + supplierkey ,  
                }).done(function( data ) {
                                       
                    data = JSON.parse(data) ; 
                    data = data[0];

                    if (firstOpened == true){
                                                firstOpened = !firstOpened;
                                                truckingPurchaseOrder.updateSupplierInformation(data.termofpaymentkey);
                                            }else if (selTermOfPaymentKey != data.termofpaymentkey ){

                                                    $( "#dialog-message" ).html("Apakah Anda ingin mengganti data pembayaran dengan data default untuk pemasok ini ?");
                                                    $( "#dialog-message" ).dialog({
                                                      width: 300,
                                                      modal: true,
                                                      title:"Konfirmasi Perubahan Data Pembayaran", 
                                                      open: function() {
                                                          $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                                                      }, 
                                                      buttons : {
                                                          OK : function (){    
                                                                truckingPurchaseOrder.updateSupplierInformation(data.termofpaymentkey);
                                                               $( this ).dialog( "close" );
                                                          },
                                                          Cancel : function (){  
                                                                $( this ).dialog( "close" );
                                                          }
                                                      } 
                                                        
                                                    });	    
                                            } 
                                       
                                    }); 

        }
     this.updateSupplierInformation =  function updateSupplierInformation (topkey){
        if ($("#" + tabID + " [name=selTermOfPaymentKey] option[value='" + topkey + "']").length > 0)
            $("#" + tabID + " [name=selTermOfPaymentKey]").val(topkey).change();  
        }
    
    }
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingPurchaseOrder = new TruckingPurchaseOrder(tabID);
        setOnDocumentReady(tabID);   
		  
         
		 
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
				}, 
			
			   
                
			   supplierName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.supplier[1]
                        }
                    } 
                }, 
			 
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?> 
        });
        
        $( "#" + tabID + " .section-panel .title" ).click(function() {  
            $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
        });
        
        $( "#" + tabID + " [name=selTermOfPaymentKey]" ).change(function() {
			for(i=0;i<cashTOP.length;i++){ 
				if ($(this).val() == cashTOP[i]){
					$( "#" + tabID + " .cashTOP").hide();
					return;
				}
			} 	
			
			$( "#" + tabID + " .cashTOP").show();
		});
        
        /*$("#"+tabID+" [name=btnShowDetail]").on('click', function() {
            var $obj = $("#" + tabID +" .div-detail-information"); 
             
            if ($obj.is(":visible")){ 
                $obj.css('display','none');
                $(this).html("<?php echo $obj->lang['showDetail']; ?>");
            }else{ 
                $obj.css('display','table');
                $(this).html("<?php echo $obj->lang['hideDetail']; ?>"); 
            }
             
        });*/
		 
		  
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValue;  
	     
		// DETAIL CLONE
		 $("#"+tabID+" [name=btnAddRows]").on('click', function() {
          	var newRow = addNewTemplateRow("detail-row-template");
             
            newRow.find(".input-datetime").removeClass("hasDatepicker");
            newRow.find(".input-datetime").removeAttr("id"); 
            newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
             
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0','truckingPurchaseOrder.updateDetail');  
             
             
        });
		 
        
       
		   
    <?php if (empty($_GET['id'])){ ?> 
        var newRow = addNewTemplateRow("detail-row-template");
        newRow.find(".input-datetime").removeClass("hasDatepicker");
        newRow.find(".input-datetime").removeAttr("id");
        newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
    <?php }  ?>
        
    
    <?php if (isset($_POST['selStatus']) && ($_POST['selStatus'] >= 2)){ ?>     
        $( "#" + tabID + " .section-panel .title" ).click();
    <?php } ?>    
          
    $( "#" + tabID + " [name=selTermOfPaymentKey]" ).change();  
            
    bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0','truckingPurchaseOrder.updateDetail');   
    
        
    //truckingPurchaseOrder.calculateCostSummary(); 
        
    //$("#" + tabID + " .transaction-detail-row").each(function() { truckingPurchaseOrder.updateCostInformation($(this)); })
 
}); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
        <?php prepareOnLoadDataForm($obj); ?>   
        <?php echo $obj->inputHidden('hidSendEmail'); ?>
        <?php echo $obj->inputHidden('hidCreditLimit'); ?>
     
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
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>   
                                      
                                        
                                  
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'supplierName',
                                                                                                           'key' => 'hidSupplierKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                ),
                                                                                'callbackFunction' => 'truckingPurchaseOrder.updateTOP()'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrder']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php     
                                                   echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $truckingServiceOrder,
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'soNumber',
                                                                                                               'key' => 'hidSOKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-trucking-service-order.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' =>  2 )
                                                                                                            ) , 
                                                                                            'allowedStatusForEdit' => array (1)
                                                                                          )
                                                                                    );  
                                                 
                                                       
                                                ?> 
                                        </div> 
                                    </div>   
                                      
                             </div>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['note']); ?></div>
                             <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                            </div>
                        </div>   
                    </div>
           </div>
      </div> 
      
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-header"  style="width:30px; text-align:right;">#</div>
                                    <div class="div-table-col detail-col-header"  style="width:50px; text-align:right;"><?php echo ucwords($obj->lang['party']); ?></div>
                                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['services']); ?></div>  
                                    <div class="div-table-col detail-col-header" style="width:170px;"><?php echo ucwords($obj->lang['note']); ?></div>  
                                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; "><?php echo ucwords($obj->lang['price']); ?></div>
                                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; "><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                    
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(array(1)); ?>" style="width:45px;"></div> 
                            </div>
                        </div>    
                    </div>  
                </div>
                
				<?php 
            
                    $totalRows = count($rsDetail); 
                  
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        $detail = 'Tidak ada data.';
                        
                        $rowNumber = 1;
                            
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = 'Pcs';
                        } else {   
                            
                            
                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] = $rsDetail[$i]['itemkey'];
                            $_POST['itemName[]'] = $rsDetail[$i]['itemname']; 
                            //$_POST['trShipmentDate[]'] = $obj->formatDBDate($rsDetail[$i]['trdate'],'d / m / Y H:i'); 
                            $_POST['priceInUnit[]'] = $obj->formatNumber($rsDetail[$i]['priceinunit']);
                            $_POST['qty[]'] = $obj->formatNumber($rsDetail[$i]['qtyinbaseunit']);
                            $_POST['amount[]'] = $obj->formatNumber($rsDetail[$i]['total']);
                            $_POST['detailNotes[]'] =  $rsDetail[$i]['trdesc']; 
                            
                        }   
                         
                ?> 
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:30px; text-align:right"><div class="row-number"><?php echo $rowNumber; ?>.</div></div>
                                <div class="div-table-col detail-col-detail" style="width:50px;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'value'=> 1,'etc' =>  'style="text-align:right;" onChange="truckingPurchaseOrder.calculateDetail(this)" ' .$etc )); ?></div>
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' =>  '  onChange="truckingPurchaseOrder.calculateDetail(this)" ' .$etc, 'allowedStatusForEdit' => array (1))); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:170px;"><?php echo $obj->inputText('detailNotes[]',array('overwritePost' => $overwrite, 'etc' => $etc, 'allowedStatusForEdit' => array (1))); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array (1) , 'etc' => 'style="text-align:right;"  onChange="truckingPurchaseOrder.calculateDetail(this)" ' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite,'readonly' => true,  'etc' =>  'style="text-align:right;"' .$etc)); ?></div>
                                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(array(1)); ?>"  style="width:45px;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="truckingPurchaseOrder.calculateTotalSales()"')); ?></div>
                            </div>
                        </div> 
                    </div>
                </div>
            
            <?php } ?>
             
                   
        </div>  
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div> 
        <div>
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>
            <!-- <div class="div-table" style="width:300px; float:right; ">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['payment']); ?> 
                        </div>  
                        <div class="div-table-col-5" style="width:180px;"> 
                             <?php echo  $obj->inputSelect('selTermOfPaymentKey',$arrTOP); ?>
                        </div> 
                    </div>


                   <?php for($i=0;$i<count($arrPaymentMethod);$i++) {   
                        $_POST['paymentMethodValue[]'] = 0;

                        if (!empty($_GET['id'])){ 
                            $rsPaymentMethod = $obj->getPaymentMethodDetail($_GET['id'],$arrPaymentMethod[$i]['pkey']);  
                            if(!empty( $rsPaymentMethod ))
                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethod[0]['amount']);
                        } 
                   ?> 
                    <div class="div-table-row  form-group cashTOP"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                <?php echo $arrPaymentMethod[$i]['name']; ?>
                        </div>  
                        <div class="div-table-col-5"> 
                                <?php echo $obj->inputHidden('paymentMethodKey[]', array ('value' => $arrPaymentMethod[$i]['pkey'])) ;?>
                                <?php echo $obj->inputNumber('paymentMethodValue[]', array ('etc' => 'style="text-align:right;" onChange="truckingPurchaseOrder.calculateTotal()"')) ;?> 
                        </div> 
                    </div> 
                    <?php } ?>

                      <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['balance']); ?> 
                        </div>  
                        <div class="div-table-col-5" style="width:180px;"> 
                            <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                        </div> 
                    </div>
            </div>   -->
            <div class="div-table" style="float:right;">
                <div class="div-table-row  form-group">
                    <div class="div-table-col-5" style="text-align:right;">
                        
                    </div> 
                </div>
                <div class="div-table-row  form-group"> 
                    <div class="div-table-col-5" style="text-align:right;">
                        <?php echo ucwords($obj->lang['subtotal']); ?> 
                    </div>  
                    <div class="div-table-col-5" style="width:200px;"> 
                        <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                    </div>
                </div>
                
                <div class="div-table-row  form-group   form-detail-field"> 
                          <div class="div-table-col-5"  style="text-align:right;">
                            <?php echo ucwords($obj->lang['tax']) . ' [Include]'; ?> 
                         </div>   
                       <div class="div-table-col-5"> 
                         <div class="flex">    
                            <div><?php echo $obj->inputCheckBox('chkIncludeTax', array('etc' => 'onChange="truckingPurchaseOrder.calculateTotal()"')); ?></div>  
                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"  onChange="truckingPurchaseOrder.calculateTotal()"')); ?></div> 
                            <div>%</div>
                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                          </div> 
                        </div>  
                        <div class="div-table-col" > </div>
                     </div> 
                
                
                <div class="div-table-row  form-group"> 
                    <div class="div-table-col-5" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?> 
                    </div>  
                    <div class="div-table-col-5"> 
                        <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                    </div>
                    <div class="div-table-col"> </div>
                </div> 
            </div>
             
        </div>
        
      
        <div style="clear:both; height:1em;"></div>    
        
        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
