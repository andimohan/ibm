<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('ARPayment.class.php','ARPrepaidTax23Payment.class.php'));
$arPrepaidTax23Payment = createObjAndAddToCol(new ARPrepaidTax23Payment());
$warehouse = createObjAndAddToCol(new Warehouse());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$customer = createObjAndAddToCol(new Customer());

$obj= $arPrepaidTax23Payment; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'arPrepaidTax23PaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';

$rsARPaymentDetail = array(); 

$_POST['trDate'] = date('d / m / Y');
$_POST['taxPeriod'] = date('F Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsARPaymentDetail = $obj->getDetailById($id);
    
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $_POST['taxObjectCode'] = $rs[0]['taxobjectcode'] ;
	$_POST['taxPeriod'] = $obj->formatDBDate($rs[0]['taxperiod'],'F Y');
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   
	$_POST['trDesc'] = $rs[0]['trnotes'];
    $_POST['refHeaderCode'] = $rs[0]['refcode'];
    $_POST['ntpn'] = $rs[0]['ntpn'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
	//$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
	//$_POST['pph23'] =  $obj->formatNumber($rs[0]['prepaidtax23']) ;
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
	 
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
	
	function ARPrepaid23Payment(tabID){
 
        this.resetDetails = function resetDetails(){  
            clearAllRows($("#"+tabID)); 
            ARPrepaid23Payment.calculateTotal(); 
        }
        
         this.updateDetail = function updateDetail(target,objAndValue,ui){   
                                        var detaiLRow = $(target).closest(".transaction-detail-row");

                                        for(i=0;i<objAndValue.length;i++){    
                                            if (objAndValue[i].type == "date")
                                               ui.item[objAndValue[i].value] = moment(ui.item[objAndValue[i].value]).format(_DATE_FORMAT_);
                                            
                                            detaiLRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).change().blur();  
                                        }
               
                                        detaiLRow.find("[name=\"arCode[]\"]").first().val(ui.item['code']);
                                    } 
         
        this.calculateTotal = function calculateTotal(){
            
            var amount = 0;    

             $("#" + tabID + " [name='chkPick[]']").each(function(){   
                    if ($(this).val() != 1 )
                        return;
                 
                    objAmount = $(this).closest(".div-table-row").find("[name='amount[]']"); 
                    amount += parseInt(unformatCurrency(objAmount.val())) || 0; 
            })     

            $("#" + tabID + " [name='total']").val(amount).blur(); 
            

        }
        
        this.importData = function importData(){ 

            $body = $("body"); 
            $body.addClass("loading");   
            var activeAjaxConnections = 0;  


            $.ajax({
                type: "GET",
                url:  'ajax-ar-prepaid-tax.php',
                beforeSend:function (xhr){
	                clearAllRows($("#"+tabID));
                    activeAjaxConnections++; 
                },
                data: "action=searchData&customerkey=" +  $("#" + tabID + " [name=hidCustomerKey]" ).val() + "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(), 
                success: function(data){ 
                        var data = JSON.parse(data);  
                        var i;
                        for(i=0;i<data.length;i++){  
                                var arrPostValue = []; 
                                arrPostValue.push({"selector":"hidARKey", "value":data[i].pkey});
                                arrPostValue.push({"selector":"arCode", "value":data[i].code}); 
                                arrPostValue.push({"selector":"refCode", "value":data[i].refcode}); 
                                arrPostValue.push({"selector":"refDate", "value":moment(data[i].refdate).format(_DATE_FORMAT_)}); 
                                arrPostValue.push({"selector":"arAmount", "value":data[i].amount}); 
                                arrPostValue.push({"selector":"outstanding", "value":data[i].outstanding}); 
                                arrPostValue.push({"selector":"amount", "value":data[i].outstanding}); 
                                addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));  
                        }

                     bindAutoCompleteForTransactionDetail('arCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' + $('#' + tabID + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail'); 
                    
                     // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                     $("#" + tabID + " .inputnumber").change().blur();
                     $("#" + tabID + " .inputdecimal").change().blur();

                    activeAjaxConnections--; 
                    if (0 == activeAjaxConnections) 
                        $body.removeClass("loading");   

                     $("#" + tabID + " [name='chkPick-master']").val(1).change(); 
                } ,
                 error: function(xhr, errDesc, exception) {
                    activeAjaxConnections--; 
                    if (0 == activeAjaxConnections) 
                        $body.removeClass("loading");   

                    }
            }); 
	    }
         
        this.onChangeChk = function onChangeChk(){ 
            ARPrepaid23Payment.calculateTotal();
        }
  
        
         this.updateCustomerInformation = function updateCustomerInformation(event, ui){
             var obj = this; 
				if ($("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val() != ''){
					$( "#dialog-message" ).html("Merubah pelanggan akan mereset detail transaksi.");
					$( "#dialog-message" ).dialog({
					  width: 300,
					  modal: true,
					  title:"Konfirmasi Perubahan Data Pelanggan", 
					  open: function() {
						  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
					  },
					  close:function() {
					  		$("#defaultForm-"+tabID + " [name=hidCustomerKey]" ).val($("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val());
							$("#defaultForm-"+tabID + " [name=customerName]" ).val($("#defaultForm-"+tabID + " [name=hidCurrentCustomerName]" ).val());
					        $(obj).closest('form').bootstrapValidator('revalidateField', $(obj).attr("name"));
                            bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete[tabID] ,'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' +  $('#' + selectedTab.newPanel[0].id + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail');
                           }, 
                          buttons : {
                              OK : function (){ 
                              		if (ui.item == null) { 
										clearAutoCompleteInput(obj,'hidCustomerKey');	
										$("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val(''); 
										$("#defaultForm-"+tabID + " [name=hidCurrentCustomerName]" ).val(''); 
				                    }else{
										$("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
										$("#defaultForm-"+tabID + " [name=hidCurrentCustomerName]" ).val(ui.item.value);  
									 }    

									ARPrepaid23Payment.resetDetails(); 
                                    $( this ).dialog( "close" );
                              },
                              Cancel : function (){    
                                    $( this ).dialog( "close" );
                              }
                          },
					});	 
				}else{

					if (ui.item == null) {
						clearAutoCompleteInput(obj,'hidCustomerKey');	
						$("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val(''); 
						$("#defaultForm-"+tabID + " [name=hidCurrentCustomerName]" ).val(''); 
					 }else{ 
						$("#defaultForm-"+tabID + " [name=hidCurrentCustomerKey]" ).val(ui.item.pkey); 
						$("#defaultForm-"+tabID + " [name=hidCurrentCustomerName]" ).val(ui.item.value); 
					 }
                    bindAutoCompleteForTransactionDetail('arCode[]',  objAndValueForDetailAutoComplete[tabID] ,'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' +  $('#' + selectedTab.newPanel[0].id + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail');
				 
                } 	 
         }
        
    }  
    
	jQuery(document).ready(function(){  
	 	   
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        ARPrepaid23Payment = new ARPrepaid23Payment(tabID);
        
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
               <?php echo $obj->submitFormScript(); ?> 
        });
		 
 
	objAndValue = new Array;
	objAndValue.push({object:'hidARKey[]', value :'pkey'}); 
    objAndValue.push({object:'refCode[]', value :'refcode'}); 	
    objAndValue.push({object:'refDate[]', value :'refdate', type : 'date'}); 	
	objAndValue.push({object:'arAmount[]', value :'amount'}); 	
	objAndValue.push({object:'outstanding[]', value :'outstanding'}); 
    objAndValue.push({object:'amount[]', value :'outstanding'});
    objAndValueForDetailAutoComplete[tabID] = objAndValue;	
	
	// DETAIL CLONE
	 $("#"+ tabID + " [name=btnAddRows]").on('click', function() {
		addNewTemplateRow("detail-row-template");
		bindAutoCompleteForTransactionDetail('arCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' +  $('#' + selectedTab.newPanel[0].id + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail');
	});
	 
	
    $("#"+ tabID + " [name=btnImport]").on('click', function() {
	   ARPrepaid23Payment.importData();
	});

    $("#"+ tabID + " [name=selWarehouseKey]").on('change', function() {
	  bindAutoCompleteForTransactionDetail('arCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' +  $('#' + selectedTab.newPanel[0].id + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail');
	});
        
   
    <?php if (empty($_GET['id'])){ ?> 
    addNewTemplateRow("detail-row-template"); 
    <?php } ?>
  	bindAutoCompleteForTransactionDetail('arCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-ar-prepaid-tax.php?action=searchData&customerkey=' +  $('#' + selectedTab.newPanel[0].id + ' [name=hidCustomerKey]' ).val()+ "&warehousekey=" +  $("#" + tabID + " [name=selWarehouseKey]" ).val(),'ARPrepaid23Payment.updateDetail');

    $("#" + tabID + " [name='chkPick-master']").val(1).change(); 

});
	 
	 
</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
    <?php prepareOnLoadDataForm($obj); ?>     
    <?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentCustomerName'); ?>
    
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						   <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxPeriod']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputMonth('taxPeriod'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                		                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['withholdingNo']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refHeaderCode'); ?>
                                        </div> 
                                    </div>
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxObjectCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('taxObjectCode'); ?>
                                        </div> 
                                    </div>
-->
                                    <div class="form-group">
                		                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['ntpn']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('ntpn'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) , 
                                                                                'callbackFunction' => 'ARPrepaid23Payment.updateCustomerInformation(event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>       
                                    <div class="form-group">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                        </div> 
                                    </div>    
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>   
                    </div>
                </div>    
        </div>    
                        
        <div class="div-table transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['tax23']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['reference']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['paymentAmount']); ?></div> 
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0" onChange="updateChkPick(this,ARPrepaid23Payment.onChangeChk);"')); ?></div> 
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php
                    $objAR = $obj->getARObj(); 
                    $totalRows = count($rsARPaymentDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
						    $rsAR = $objAR->getDataRowById($rsARPaymentDetail[$i]['arkey']);   
                            

                            $_POST['hidDetailKey[]'] =  $rsARPaymentDetail[$i]['pkey']; 
                            $_POST['hidARKey[]'] =  $rsARPaymentDetail[$i]['arkey']; 
                            $_POST['arCode[]'] =  $rsAR[0]['code'] ;
                            $_POST['refCode[]'] = $rsAR[0]['refcode'] ;
                            $_POST['refDate[]'] =   $obj->formatDBDate($rsAR[0]['refdate'],'d / m / Y');
                      		$_POST['arAmount[]'] =  $obj->formatNumber($rsAR[0]['amount']);
							$_POST['outstanding[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['amount']); 
                            $_POST['chkPick[]'] =  1;
                            
                        }
                       
                 ?>        
                 
                  <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                            <?php echo $obj->inputText('arCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?>
                            <?php echo $obj->inputHidden('hidARKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => true)); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('refDate[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:center"')); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('arAmount[]',array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right"; onChange="ARPrepaid23Payment.calculateTotal()"; ')); ?></div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px; text-align:center"><?php echo $obj->inputCheckBox('chkPick[]',  array('etc' => 'onChange="updateChkMaster(this,ARPrepaid23Payment.onChangeChk)" ' ) ); ?></div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="ARPrepaid23Payment.calculateTotal()"')); ?> </div>
                </div>
            
                <?php  } ?>   
                   
         </div>        
                   
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
               
          <div class="div-table transaction-detail" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                            Total 
                    </div>  
                    <div class="div-table-col-3" style="width:110px"> 
                           <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?>
                    </div> 
                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:35px;"></div> 
                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                </div>  
          </div>     

        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
