<?php 
require_once '../_config.php'; 
require_once '../_include.php';  


$obj= $preorder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'preOrderList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];


$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editSalesInactiveCriteria = '';

$chkItemFilter = '';
$isPriceIncludeTax = '';
$rsPreOrderDetail = array();

$_POST['trDate'] = date('d / m / Y');

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
    
	$rsPreOrderDetail = $obj->getDetailById($id);
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trNotes'] = $rs[0]['trnotes'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount']);
	$_POST['pointValue'] = $obj->formatNumber($rs[0]['pointvalue']);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
	$_POST['selSalesKey'] = $rs[0]['saleskey']; 
 
	$_POST['recipientName'] = $rs[0]['recipientname'];
	$_POST['recipientPhone'] = $rs[0]['recipientphone'];
	$_POST['recipientEmail'] = $rs[0]['recipientemail'];
	$_POST['recipientAddress'] = $rs[0]['recipientaddress']; 
	
	if(!empty($rs[0]['ispriceincludetax'])) 
	$isPriceIncludeTax = 'checked="checked"';
	
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage']);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
    $_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
	
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	$editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
 
	$_POST['action'] = 'edit';
}else{
	
	$_POST['action'] = 'add';
	
	if($useAutoCode == 1) 
		$_POST['code'] = 'XXXXXXXX';
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrTOP = $class->convertForCombobox($termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')'),'pkey','name'); 
$arrPaymentMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')');
$arrSales = $class->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
	  
	jQuery(document).ready(function(){  
	 	 
		$("#" + selectedTab.newPanel[0].id + " #defaultForm").attr("id","defaultForm-"+selectedTab.newPanel[0].id);   
  
       $("#defaultForm-"+selectedTab.newPanel[0].id+" .remove-button").click(function() {removeDetailRows(this);});  
		
		 
		 $('#defaultForm-' + selectedTab.newPanel[0].id )
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
			
			   customerName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.customer[1]
                        }, 
                    }
                },   
				
			 
            }
        })
        .on('success.form.bv', function(e) {
                 submitForm( e,
                          {tabID : tabID },
                          {parentPanelId : "<?php echo $parentPanelId; ?>", parentTitle : "<?php echo $parentTitle; ?>" }, 
                         ); 
        });
		
		 $("#" + selectedTab.newPanel[0].id + " .inputnumber")
			 .each(function() {  
				if($(this).val() == "") $(this).val(0); 
			 })
			 .bind( "blur", function(event) { 
			   inputNumberOnBlur($(this));
	 	});
 
		    
		$( "#" + selectedTab.newPanel[0].id + " [name=customerName]" ).autocomplete({
		  source: "ajax-customer.php?action=searchData",
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#defaultForm-"+selectedTab.newPanel[0].id + " [name=hidCustomerKey]" ).val(ui.item.pkey); 
			},   
		  change: function( event, ui ) { 
		  		 if (ui.item == null) 
					clearAutoCompleteInput(this,'hidCustomerKey');
				 
			},
		}).change(function() {
		   if ($(this).val() == "") 
					clearAutoCompleteInput(this,'hidCustomerKey'); 
		});
	
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'}); 
        objAndValueForDetailAutoComplete[tabID] = objAndValue; 
	
	
		// DETAIL CLONE
		 $("#defaultForm-"+selectedTab.newPanel[0].id+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("pre-order-row-template");
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData','updateLatestSellingPrice'); 
        });
		
 
	$("#" + selectedTab.newPanel[0].id + " .form-detail-field").toggle(); 
	$("#" + selectedTab.newPanel[0].id + " .form-detail-button").click(function() {  
	
		$("#" + selectedTab.newPanel[0].id + " .form-detail-field").toggle( "highlight" );
	 	var temp = $("#" + selectedTab.newPanel[0].id + " .form-detail-button").attr("relalt");   
		$("#" + selectedTab.newPanel[0].id + " .form-detail-button").attr("relalt",$("#" + selectedTab.newPanel[0].id + " .form-detail-button").text());
		$("#" + selectedTab.newPanel[0].id + " .form-detail-button").text(temp);
	
	}); 
	
	$("#" + selectedTab.newPanel[0].id + " [name=btnSaveEmail]").click(function() {  
	 	$("#" + selectedTab.newPanel[0].id + " [name=hidSendEmail]").val(1);
		$("#" + selectedTab.newPanel[0].id + " #defaultForm").submit();
	}); 
	
	
	
});
	
	 function updateLatestSellingPrice(target,objAndValue,ui){
		  //update selling price
		   
		  var customerkey;
		  customerkey = $( "#" + selectedTab.newPanel[0].id + " [name=hidCustomerKey]" ).val(); 
		  
		    $.ajax({
					type: "POST",
					url:  'getLatestSellingPrice.php',
					data: "itemkey="+ui.item.pkey+"&customerkey=" + customerkey ,  
					success: function(data){ 
					 	
						  for(i=0;i<objAndValue.length;i++){  
								  $(target).closest(".div-table-row").find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
						  }
						  
						 var temp = JSON.parse(data); 
						 if (temp.sellingprice != null){ 
							 $(target).closest(".div-table-row").find("[name='priceInUnit[]']").first().val(temp.sellingprice).change().blur(); 
						 } 
			
					} 
				}) 
				
				
		
		  
		 	//$(obj).closest(".div-table-row").find("[name='priceInUnit[]']").first().val("99")); 
			//console.dir(ui.item);
			//alert(objTarget.attr("name")); 
			//alert(ui.item.sellingprice)  ;
	 }
	 
	function preOrderCalculateDetail(obj){    
		var parentObj =  $(obj).parent().parent();
		var qty =  unformatCurrency(parentObj.find("[name='qty[]']").val());
		var priceInUnit =  unformatCurrency(parentObj.find("[name='priceInUnit[]']").val());
		var discount =  unformatCurrency(parentObj.find("[name='discountValueInUnit[]']").val());
		var discountType =  unformatCurrency(parentObj.find("[name='selDiscountType[]']").val());
		 
		if (discount != 0){
			if (discountType == 2)
				discount = discount/100 * priceInUnit;
		}
		
		var subtotal = qty * (priceInUnit - discount);
		parentObj.find("[name='subtotal[]']").val(subtotal).blur(); 
	
		preOrderCalculateTotal();
	}
	
	function preOrderCalculateTotal(){ 
		var selectedTabObj =  selectedTab.newPanel[0].id;
		
		var subtotal = 0; 
		$("#" + selectedTabObj + " [name='subtotal[]']").each(function() {   
				subtotal += parseInt(unformatCurrency($(this).val())) || 0;
		})
		
		$("#" + selectedTabObj + " [name='subtotal']").val(subtotal).blur();
		
		var finalDiscount = parseInt(unformatCurrency($("#" + selectedTabObj + " [name='finalDiscount']").val())) || 0 ;
		var finalDiscountType = parseInt(unformatCurrency($("#" + selectedTabObj + " [name='selFinalDiscountType']").val())) || 0 ;
		var pointValue = parseInt(unformatCurrency($("#" + selectedTabObj + " [name='pointValue']").val())) || 0 ;
		  
		var includeTax =   $("#" + selectedTabObj + " [name='chkIncludeTax']").prop("checked");
		var taxPercentage =  parseInt(unformatCurrency($("#" + selectedTabObj + " [name='taxPercentage']").val())) || 0 ; 
		
		if (finalDiscount != 0){
			if (finalDiscountType == 2)
				finalDiscount = finalDiscount/100 * subtotal;
		}
		
		subtotal -= finalDiscount;
		subtotal -= pointValue;
		
		$("#" + selectedTabObj + " [name='beforeTaxTotal']").val(subtotal).blur();
		
		var taxValue = 0;
		if (includeTax == false) {
				taxValue = subtotal * taxPercentage / 100;
				subtotal += taxValue;
		}else{
		   		taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
				$("#" + selectedTabObj + " [name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
		}
		
		$("#" + selectedTabObj + " [name='taxValue']").val(taxValue).blur(); 
		 
		var total = subtotal;
		$("#" + selectedTabObj + " [name='total']").val(total).blur();
		
		var totalPayment = 0; 
		$("#" + selectedTabObj + " [name='paymentMethodValue[]']").each(function() {   
				totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
		}) 
		
		var balance = totalPayment - total;
		$("#" + selectedTabObj + " [name='balance']").val(balance).blur();
		 
	}
</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
   	<?php echo $obj->input('hidden','hidId'); ?>
   	<?php echo $obj->input('hidden','hidModifiedOn'); ?>
    <?php echo $obj->input('hidden','action'); ?>
    <?php echo $obj->input('hidden','hidCustomerKey'); ?>
    <?php echo $obj->input('hidden','hidSendEmail'); ?>
    
        <div class="div-table" style="width:100%; ">
                <div class="div-table-row">
                    <div class="div-table-col"  style="width:49%; text-align:center"> 
      						  <div class="div-table-tab-form" style="margin:auto;"> 
              <div class="div-table-caption border-orange">Informasi Umum</div>
              <div class="div-table-row form-group">
                <div class="div-table-col-5 div-table-col-header">
                    <label class="col-lg-1 control-label">Status</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo  $obj->inputSelect('selStatus', $arrStatus, true,0,'disabled="disabled"'); ?>
                    </div>
                </div> 
             </div>
            
            
             <?php if($useAutoCode == 1)    
                $code = $obj->input('text','code',true,'','readonly="readonly"', 'form-control readonly');  
            else  
                $code =  $obj->input('text','code');   ?>
        
    
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Kode</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo  $code; ?>
                    </div>
                </div> 
             </div>
            
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Tanggal</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                          <?php echo $obj->input('text','trDate',true,'','readonly="readonly"','form-control input-date'); ?>
                    </div> 
                </div> 
             </div>  
             
            <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Gudang</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                    </div>
                </div> 
             </div> 
            
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Pelanggan</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12">                                                                                                                         
                          <?php echo $obj->input('text','customerName'); ?>
                    </div> 
                </div> 
             </div>  
			 <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Sales</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                           <?php echo  $obj->inputSelect('selSalesKey', $arrSales); ?>
                    </div>
                </div> 
             </div>  
         </div>
            
             <div class="div-table-tab-form" style="margin:auto; margin-top:3em">
                    <div class="div-table-caption border-green">Catatan</div>
                    <div class="div-table-row form-group"> 
                        <div class="div-table-col-5">
                            <div class="col-lg-12">  
                                  <?php echo  $obj->inputTextArea('trNotes',true,'','style="height:14em;"'); ?>
                            </div> 
                        </div> 
                    </div>   
              </div>    
              
 
         </div> 
             
            <div class="div-table-col"  style="width:2%; text-align:center"> </div>
            <div class="div-table-col"  style="width:49%; text-align:center">
				   <div class="div-table-tab-form" style="margin:auto; "> 
                          <div class="div-table-caption border-blue">Tujuan Pengiriman</div>
                          
                          <div class="div-table-row form-group">
                            <div class="div-table-col-5 div-table-col-header">
                                <label class="col-lg-1 control-label">Nama</label>
                            </div> 
                            <div class="div-table-col-5">
                                <div class="col-lg-12"> 
                                      <?php echo $obj->input('text','recipientName'); ?>
                                </div>
                            </div> 
                         </div>
                        
                          <div class="div-table-row form-group">
                            <div class="div-table-col-5 div-table-col">
                                <label class="col-lg-1 control-label">Telepon</label>
                            </div> 
                            <div class="div-table-col-5">
                                <div class="col-lg-12"> 
                                      <?php echo $obj->input('text','recipientPhone'); ?>
                                </div>
                            </div> 
                         </div>
                        
                        
                          <div class="div-table-row form-group">
                            <div class="div-table-col-5 div-table-col">
                                <label class="col-lg-1 control-label">Email</label>
                            </div> 
                            <div class="div-table-col-5">
                                <div class="col-lg-12"> 
                                      <?php echo $obj->input('text','recipientEmail'); ?>
                                </div>
                            </div> 
                         </div>
                        
                          <div class="div-table-row form-group">
                            <div class="div-table-col-5 div-table-col">
                                <label class="col-lg-1 control-label">Alamat</label>
                            </div> 
                            <div class="div-table-col-5">
                                <div class="col-lg-12"> 
                                        <?php echo  $obj->inputTextArea('recipientAddress',true,'','style="height:8em;"'); ?>
                                </div>
                            </div> 
                         </div> 
                      
                     </div>  
			  </div>
        </div>
                    
         </div>  
                       
        <div style="clear:both; height:2em;"></div>
        
        <div class="div-table" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header">Item</div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Harga @</div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right; padding-right:0;"></div>
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right; padding-left:0.2em;">Diskon @</div>
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;">Subtotal</div>
                    <div class="div-table-col detail-col-header"  style="width:70px"></div>
                </div>
                
				<?php
                  	 
                    echo '<script>';  
                            
                    for ($i=0;$i<count($rsPreOrderDetail); $i++){ 
					
						$rsItem = $item->getDataRowById($rsPreOrderDetail[$i]['itemkey']);
							
                        if ($i==0){ 
							
                            $_POST['hidItemKey[]'] =  $rsPreOrderDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsItem[0]['name']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsPreOrderDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPreOrderDetail[$i]['priceinunit']); 
                            $_POST['selDiscountType[]'] =  $rsPreOrderDetail[$i]['discounttype'] ; 
                            $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsPreOrderDetail[$i]['discount']); 
                            $_POST['subtotal[]'] =   $obj->formatNumber($rsPreOrderDetail[$i]['total']); 
                        }else{  
						 
                            $arrPostValue = array();
                            array_push($arrPostValue,array("selector" => 'hidItemKey', "value" =>   $rsPreOrderDetail[$i]['itemkey'])) ;
                            array_push($arrPostValue,array("selector" => 'itemName', "value" =>   $rsItem[0]['name'])) ;
                            array_push($arrPostValue,array("selector" => 'qty', "value" =>    $obj->formatNumber($rsPreOrderDetail[$i]['qty']))) ;
                            array_push($arrPostValue,array("selector" => 'priceInUnit', "value" =>    $obj->formatNumber($rsPreOrderDetail[$i]['priceinunit']))) ;
                            array_push($arrPostValue,array("selector" => 'selDiscountType', "value" =>   $rsPreOrderDetail[$i]['discounttype'])) ;
                            array_push($arrPostValue,array("selector" => 'discountValueInUnit', "value" =>    $obj->formatNumber($rsPreOrderDetail[$i]['discount']))) ; 
                            array_push($arrPostValue,array("selector" => 'subtotal', "value" =>    $obj->formatNumber($rsPreOrderDetail[$i]['total']))) ;
                             echo 'addNewTemplateRow("pre-order-row-template",\''.str_replace("'","\'",json_encode($arrPostValue)).'\'); ';
                        }
                         
                    }	
                   
				    echo 'bindAutoCompleteForTransactionDetail(\'itemName[]\',objAndValueForDetailAutoComplete[tabID],\'ajax-item.php?action=searchData\',\'updateLatestSellingPrice\');'; 
                    echo '</script>'; 
                ?>
                
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',true,'',''); ?><?php echo $obj->input('hidden','hidItemKey[]'); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','qty[]',true,'','style="text-align:right;"  onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','priceInUnit[]',true,'','style="text-align:right;" onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail" style="padding-right:0;"><?php echo $obj->input('text','discountValueInUnit[]',true,'','style="text-align:right;"  onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail" style="padding-left:0.2em;"><?php echo $obj->inputSelect('selDiscountType[]', $obj->arrDiscountType,true,'',' style="width:5em;"  onChange="preOrderCalculateDetail(this)"'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','subtotal[]',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="preOrderCalculateDetail()"','btn btn-link remove-button'); ?></div>
               </div>
                
                  
                 <!-- Template for dynamic field -->  
                 <div class="div-table-row pre-order-row-template" style="display:none;"  > 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',false,'','disabled="disabled"'); ?> <?php echo $obj->input('hidden','hidItemKey[]',false,'','disabled="disabled"'); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','qty[]',false,'','disabled="disabled" style="text-align:right;"  onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','priceInUnit[]',false,'',' style="text-align:right;"   onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail" style="padding-right:0;"><?php echo $obj->input('text','discountValueInUnit[]',false,'','disabled="disabled" style="text-align:right;"  onChange="preOrderCalculateDetail(this)"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail" style="padding-left:0.2em;"><?php echo  $obj->inputSelect('selDiscountType[]', $obj->arrDiscountType,false,0,' disabled="disabled"  style="width:5em;"  onChange="preOrderCalculateDetail(this)"'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','subtotal[]',false,'','disabled="disabled" readonly="readonly" style="text-align:right; "','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="preOrderCalculateDetail()"','btn btn-link remove-button'); ?></div> 
                  </div>   
                    
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->input('button','btnAddRows',false,$obj->lang['addRows'],'style="margin-top:0.2em;"'); ?></div>
             
        
          <div style="margin-right:68px;">   
         
                      <div class="div-table  form-detail-field" style="width:300px; float:right; ">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;">
                                    Pembayaran 
                                </div>  
                                <div class="div-table-col-5" style="width:180px;"> 
                                     <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
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
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;"> 
                                        <?php echo $arrPaymentMethod[$i]['name']; ?>
                                </div>  
                                <div class="div-table-col-5"> 
                                       	<?php echo $obj->input('hidden','paymentMethodKey[]',false,$arrPaymentMethod[$i]['pkey']) ;?>
   									    <?php echo $obj->input('text','paymentMethodValue[]',true,'','style="text-align:right;" onChange="preOrderCalculateDetail()"','form-control inputnumber'); ?> 
                                </div> 
                            </div> 
                            <?php } ?>
                            
                              <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;">
                                    Balance 
                                </div>  
                                <div class="div-table-col-5" style="width:180px;"> 
   									    <?php echo $obj->input('text','balance',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?> 
                                </div> 
                            </div>
                          
                      </div>   
               
                      <div class="div-table" style="float:right;">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;">
                                    Subtotal 
                                </div>  
                                <div class="div-table-col-5" style="width:180px;"> 
                                     <?php echo $obj->input('text','subtotal',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?> 
                                </div>
                                
                            </div>
                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5"  style="text-align:right;">
                                     Diskon
                                </div>  
                                <div class="div-table-col-5"> 
                                     <div style="float:right; padding-left:0.5em;  width:110px;  "><?php echo $obj->input('text','finalDiscount',true,'','style="text-align:right;"  onChange="salesOrderCalculateTotal()"','form-control inputnumber'); ?></div>
                                     <div style="float:right;   width:60px; "><?php echo $obj->inputSelect('selFinalDiscountType', $obj->arrDiscountType,true,'', ' onChange="salesOrderCalculateTotal()"'); ?> </div>
                                </div>
                                <div class="div-table-col" > </div>
                            </div>
                           <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5"  style="text-align:right;">
                                     Point
                                </div>  
                                <div class="div-table-col-5"> 
                                     <?php echo $obj->input('text','pointValue',true,'','style="text-align:right;"  onChange="salesOrderCalculateTotal()"','form-control inputnumber'); ?>
                                </div>
                                <div class="div-table-col" > </div>
                            </div>
                             
                             <div class="div-table-row  form-group   form-detail-field"> 
                                <div class="div-table-col-5" style="text-align:right; padding-top:2em;">
                                    Sebelum Pajak
                                </div>  
                                <div class="div-table-col-5" style="padding-top:2em;"> 
                                     <?php echo $obj->input('text','beforeTaxTotal',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?> 
                                </div>
                                
                            </div>
                            
                             <div class="div-table-row  form-group   form-detail-field"> 
                                  <div class="div-table-col-5"  style="text-align:right;">
                                    Pajak [Include]
                                 </div>   
                                 <div class="div-table-col-5"> 
                                        <div style="float:right; padding-left:0.5em;  width:95px; "><?php echo $obj->input('text','taxValue',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?></div>
                                        <div style="float:right; padding-left:0.5em; line-height:3em;" >% </div>
                                        <div style="float:right; padding-left:0.5em;  width:45px;"> <?php echo $obj->input('text','taxPercentage',true,'','style="text-align:right;"  onChange="salesOrderCalculateTotal()"','form-control inputnumber'); ?></div> 
                                        <div style="float:right; padding-top:0.5em;"><input type="checkbox" name="chkIncludeTax"  value="1"  onChange="salesOrderCalculateTotal()" <?php echo $isPriceIncludeTax; ?>/></div>  
                                </div>
                                <div class="div-table-col" > </div>
                             </div>   
                            
                           <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;"> 
                                        Total 
                                </div>  
                                <div class="div-table-col-5"> 
                                        <?php echo $obj->input('text','total',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?> 
                                </div>
                                <div class="div-table-col"> </div>
                            </div> 
                             
                      </div>    
      				 
      				  <div style="clear:both"></div>
                      <div class="form-detail-button" style="float:right; text-align:right;" relalt="Sembunyikan Detail">Tampilkan Detail</div>
        </div>
         
       <div style="clear:both"></div>
       
        <div class="form-button-panel" > 
       	 <?php if (empty($_GET['id']) || $_POST['selStatus'] == 1){ ?>
         <?php  echo $obj->generateSaveButton(); echo ' ' ; ?>
         <?php 
				if($security->isAdminLogin($obj->securityObject,11,false)) {
					$totalSent = 0 ;
					if (!empty($rs))
						$totalSent = $rs[0]['invoiceSent'];
						
					echo $obj->input('submit','btnSaveEmail',false,'Simpan & Email ('.$totalSent.')'); 
				}
		?> 
        <?php } ?>
        </div> 
        
    </form>  
     <div class="data-history"> 
      <div class="content">
          <?php
            if (!empty($id)){
                $rs = $obj->generateDataHistory($id); 
                echo $obj->compileDataHistoryForAdminForm($rs);
            }
          ?>
        </div>
    </div>
</div> 
</body>

</html>