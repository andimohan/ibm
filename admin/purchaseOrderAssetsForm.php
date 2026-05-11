<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $purchaseOrderAssets;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'purchaseOrderAssetsList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];


$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
 
$ispriceincludetax = '';
$isfullreceive = 'checked="checked"';
$rsPurchaseDetail = array();

$_POST['trDate'] = date('d / m / Y');
  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
    
	$rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id);
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['overwriteRate'] = $obj->formatNumber($rs[0]['rate']);
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
     
 
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 

	if(!empty($rs[0]['ispriceincludetax'])) 
	$ispriceincludetax = 'checked="checked"';

    $isfullreceive = '';
    if(!empty($rs[0]['isfullreceive'])) 
	$isfullreceive = 'checked="checked"';
	
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
	
    $_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	 
	$_POST['action'] = 'edit';
}else{
	
	$_POST['action'] = 'add'; 
    
	if($useAutoCode == 1) 
		$_POST['code'] = 'XXXXXXXX';
}

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')');

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name'); 
$arrPaymentMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'); 
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrAssetsCategory = $class->convertForCombobox($assetsCategory->searchData(),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">

	 var cashTOP = Array();
     var firstOpened = true; //temporary until i figure something out
    
	 <?php 
		for ($i=0;$i<count($rsTOP);$i++){
			if ($rsTOP[$i]['duedays'] <> 0)
				echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
		}
	 ?> 
	  
    function PurchaseOrderAssets(tabID) { 
         this.conversion = {};
        
         this.updateDetail = function updateDetail(target,objAndValue,ui){   
                                        var detaiLRow = $(target).closest(".transaction-detail-row");
                                        this.calculateDetail(detaiLRow.find("[name='" + objAndValue[0].object +"']").first());
                            } 
      
 
          this.calculateDetail = function calculateDetail(obj){   
              
                            var parentObj =  $(obj).parent().parent(); 
                     
                            var qty =  unformatCurrency(parentObj.find("[name='qty[]']").val());
                            var priceInUnit =  unformatCurrency(parentObj.find("[name='priceInUnit[]']").val());  
                             

                            var subtotal = qty * (priceInUnit);
                            parentObj.find("[name='subtotal[]']").val(subtotal).blur();  

                            this.calculateTotal();
                        }

           this.calculateTotal =  function calculateTotal(){ 
                                            

                                            var subtotal = 0; 
                                            $("#" + tabID + " [name='subtotal[]']").each(function() {   
                                                    subtotal += parseInt(unformatCurrency($(this).val())) || 0;
                                            })

                                            $("#" + tabID + " [name='subtotal']").val(subtotal).blur();

                                            var shipmentFee = parseInt(unformatCurrency($("#" + tabID + " [name='shipmentFee']").val())) || 0 ; 
                                            var etcCost = parseInt(unformatCurrency($("#" + tabID + " [name='etcCost']").val())) || 0 ; 

                                            var includeTax =   $("#" + tabID + " [name='chkIncludeTax']").prop("checked");
                                            var taxPercentage =  parseInt(unformatCurrency($("#" + tabID + " [name='taxPercentage']").val())) || 0 ; 
 
                                            $("#" + tabID + " [name='beforeTaxTotal']").val(subtotal).blur();

                                            var taxValue = 0;
                                            if (includeTax == false) {
                                                taxValue = subtotal * taxPercentage / 100;
                                                subtotal += taxValue;
                                            }else{
                                                taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                                                $("#" + tabID + " [name='beforeTaxTotal']").val(subtotal - taxValue).blur(); 
                                            }

                                            $("#" + tabID + " [name='taxValue']").val(taxValue).blur(); 

                                            var total = subtotal +  shipmentFee + etcCost;
                                            $("#" + tabID + " [name='total']").val(total).blur();

                                            var totalPayment = 0; 
                                            $("#" + tabID + " [name='paymentMethodValue[]']").each(function() {   
                                                totalPayment += parseInt(unformatCurrency($(this).val())) || 0;
                                            }) 

                                            var balance = totalPayment - total;
                                            $("#" + tabID + " [name='balance']").val(balance).blur();

           }
           
           this.updateTOP = function updateTOP(){
          
                                var selTermOfPaymentKey = $( "#" + tabID + " [name=selTermOfPaymentKey]" ).val();   
                                var supplierkey = $( "#" + tabID + " [name=hidSupplierKey]" ).val(); 

                                   $.ajax({
                                        type: "POST",
                                        url:  'getSupplierInformation.php',
                                        data: "supplierkey=" + supplierkey ,  
                                    }).done(function( data ) {
                                       
                                            data = JSON.parse(data) ; 
                                            data = data[0];

                                            if (firstOpened == true){
                                                firstOpened = !firstOpened;
                                                purchaseOrderAssets.updateSupplierInformation(data.termofpaymentkey);
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
                                                                purchaseOrderAssets.updateSupplierInformation(data.termofpaymentkey);
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
        
         var tabID = selectedTab.newPanel[0].id;
         purchaseOrderAssets = new PurchaseOrderAssets(tabID);
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
                            message: phpErrorMsg.supplier[1]
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
		
		$( "#" + tabID + " [name=selTermOfPaymentKey]" ).change(function() {
			for(i=0;i<cashTOP.length;i++){ 
				if ($(this).val() == cashTOP[i]){
					$( "#" + tabID + " .cashTOP").hide();
					return;
				}
			} 	
			
			$( "#" + tabID + " .cashTOP").show();
		});
		
 		
		$( "#" + tabID + " [name=chkIsFullReceive]" ).change(function() { 
           var shipmentFee =  $(this).closest("form").find("[name=shipmentFee]");
            
            shipmentFee.prop("readonly",!$(this).prop("checked")); 
              
            if (!$(this).prop("checked")){ 
                shipmentFee.prop("oldValue",shipmentFee.val()); 
                shipmentFee.val(0);
            }else{
                 
                if (shipmentFee.prop("oldValue") != undefined)
                    shipmentFee.val(shipmentFee.prop("oldValue"));
            }
            
		});
        
		$( "#" + tabID + " [name=chkIsFullReceive]" ).change(); 
        
		$( "#" + tabID + " [name=supplierName]" ).autocomplete({
		  source: "ajax-supplier.php?action=searchData",
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#defaultForm-"+tabID + " [name=hidSupplierKey]" ).val(ui.item.pkey); 
			},   
		  change: function( event, ui ) { 
		  		 if (ui.item == null) 
					clearAutoCompleteInput(this,'hidSupplierKey');
				 
                purchaseOrderAssets.updateTOP();
			},
		}).change(function() {
		   if ($(this).val() == "") 
					clearAutoCompleteInput(this,'hidSupplierKey'); 
		});
	 
		 
        
		// DETAIL CLONE
		 $("#defaultForm-"+tabID+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("purchase-order-assets-row-template"); 
        });
   
        $("#" + tabID + " .form-detail-field").toggle(); 
        $("#" + tabID + " .form-detail-button").click(function() {   
            $("#" + tabID + " .form-detail-field").toggle( "highlight" );
            var temp = $("#" + tabID + " .form-detail-button").attr("relalt");   
            $("#" + tabID+ " .form-detail-button").attr("relalt",$("#" + tabID + " .form-detail-button").text());
            $("#" + tabID + " .form-detail-button").text(temp); 
        });
	 
	$( "#" + tabID + " [name=selTermOfPaymentKey]" ).change(); 
         
    <?php if (empty($_GET['id'])){ ?> 
        addNewTemplateRow("purchase-order-assets-row-template");
    <?php } ?>     
  	  	  
});
    
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
   	<?php echo $obj->input('hidden','hidId'); ?>
   	<?php echo $obj->input('hidden','hidModifiedOn'); ?>
    <?php echo $obj->input('hidden','action'); ?>
    <?php echo $obj->input('hidden','hidSupplierKey'); ?>
    
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                <div class="div-table-caption border-orange">Informasi Umum</div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Status</label> 
                                    <div class="col-xs-9"> 
                                         <?php echo  $obj->inputSelect('selStatus', $arrStatus, true,0,'disabled="disabled"'); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Kode</label> 
                                    <div class="col-xs-9"> 
                                       <?php  echo ($useAutoCode == 1) ? $obj->input('text','code',true,'','readonly="readonly"', 'form-control readonly') : $obj->input('text','code'); ?>
                                    </div> 
                                </div>   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Tanggal</label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->input('text','trDate',true,'','readonly="readonly"','form-control input-date'); ?>
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Gudang</label> 
                                    <div class="col-xs-9"> 
                                        <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                    </div> 
                                </div>     
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Pemasok</label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->input('text','supplierName'); ?>
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">Terima Penuh</label> 
                                    <div class="col-xs-9"> 
                                        <input type="checkbox" style="margin-top:0.8em;"  name="chkIsFullReceive" onclick="return false" value="1"  <?php echo $isfullreceive; ?>/>
                                    </div> 
                                </div>    
                             </div>
         			</div> 
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue">Catatan</div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc',true,'','style="height:10em;"'); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div>
                    
         </div>   
         
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header">Nama Aset</div>
                    <div class="div-table-col detail-col-header">Kategori</div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jml</div> 
                    <div class="div-table-col detail-col-header" style="width:140px; text-align:right;">Harga @</div> 
                    <div class="div-table-col detail-col-header" style="width:110px; text-align:right;">Nilai Residu</div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Manfaat (Bln)</div> 
                    <div class="div-table-col detail-col-header" style="width:140px; text-align:right;">Subtotal</div>
                    <div class="div-table-col detail-col-header" style="width:70px"></div>
                </div>
              
                <?php 
                    for ($i=0;$i<count($rsPurchaseDetail); $i++){  
                         
                        $_POST['itemName[]'] =  $rsPurchaseDetail[$i]['itemname'];   
                        $_POST['qty[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qty']); 
                        $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']);   
                        $_POST['lifeCycle[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['lifecycle']);  
                        $_POST['residueValue[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['residuevalue']);  
                        $_POST['subtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['total']); 
                        $_POST['selAssetsCategory[]'] = $rsPurchaseDetail[$i]['categorykey'];  
                          
                ?>
                 <div class="div-table-row transaction-detail-row"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',true,'',''); ?></div>  
                    <div class="div-table-col detail-col-detail"><?php echo  $obj->inputSelect('selAssetsCategory[]', $arrAssetsCategory); ?></div>  
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('text','qty[]',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('text','priceInUnit[]',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('text','residueValue[]',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('text','lifeCycle[]',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('text','subtotal[]',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="purchaseOrderAssets.calculateTotal()"','btn btn-link remove-button'); ?></div>
                 </div> 
                <?php  }   ?> 
            
                 <!-- Template for dynamic field -->  
                 <div class="div-table-row purchase-order-assets-row-template" style="display:none;"  > 
                    <div class="div-table-col detail-col-detail"> <?php echo $obj->input('text','itemName[]',false,'','disabled="disabled"'); ?>  </div> 
                    <div class="div-table-col detail-col-detail"> <?php echo  $obj->inputSelect('selAssetsCategory[]', $arrAssetsCategory); ?></div>  
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('text','qty[]',false,'','disabled="disabled" style="text-align:right;"  onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('text','priceInUnit[]',false,'','disabled="disabled" style="text-align:right;"   onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('text','residueValue[]',false,'','disabled="disabled" style="text-align:right;"   onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div> 
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('text','lifeCycle[]',false,'','disabled="disabled" style="text-align:right;"   onChange="purchaseOrderAssets.calculateDetail(this)"','form-control inputnumber'); ?></div>  
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('text','subtotal[]',false,'','disabled="disabled" readonly="readonly" style="text-align:right; "','form-control inputnumber'); ?></div>
                    <div class="div-table-col detail-col-detail"  style="vertical-align:top;"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="purchaseOrderAssets.calculateTotal()"','btn btn-link remove-button'); ?></div> 
                  </div>    
                   
         </div>        
      
     
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->input('button','btnAddRows',false,$obj->lang['addRows'],'style="margin-top:0.2em;"'); ?></div>
              
          <div style="margin-right:72px;">   
         
                      <div class="div-table" style="width:300px; float:right; ">
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
                            <div class="div-table-row  form-group cashTOP"> 
                                <div class="div-table-col-5" style="text-align:right;"> 
                                        <?php echo $arrPaymentMethod[$i]['name']; ?>
                                </div>  
                                <div class="div-table-col-5"> 
                                       	<?php echo $obj->input('hidden','paymentMethodKey[]',false,$arrPaymentMethod[$i]['pkey']) ;?>
   									    <?php echo $obj->input('text','paymentMethodValue[]',true,'','style="text-align:right;" onChange="purchaseOrderAssets.calculateTotal()"','form-control inputnumber'); ?> 
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
                                <div class="div-table-col-5" style="width:200px;"> 
                                     <?php echo $obj->input('text','subtotal',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?> 
                                </div>
                                
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
                                        <div style="float:right; padding-left:0.5em;  width:100px; "><?php echo $obj->input('text','taxValue',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?></div>
                                        <div style="float:right; padding-left:0.5em; line-height:3em;" >% </div>
                                        <div style="float:right; padding-left:0.5em;  width:60px;"> <?php echo $obj->input('text','taxPercentage',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateTotal()"','form-control inputdecimal'); ?></div> 
                                        <div style="float:right; padding-top:0.5em;"><input type="checkbox" name="chkIncludeTax"  value="1"  onChange="purchaseOrderAssets.calculateTotal()" <?php echo $ispriceincludetax; ?>/></div>  
                                </div>
                                <div class="div-table-col" > </div>
                             </div>   
                                
                             <div class="div-table-row  form-group   form-detail-field"> 
                                <div class="div-table-col-5"  style="text-align:right; padding-top:2em;">
                                     Ongkos Kirim 
                                </div>  
                                <div class="div-table-col-5" style=" padding-top:2em;" > 
                                        <?php echo $obj->input('text','shipmentFee',true,'','style="text-align:right;" onChange="purchaseOrderAssets.calculateTotal()" ','form-control inputnumber'); ?>
                                </div>
                                <div class="div-table-col" > </div>
                            </div>
                            
                             <div class="div-table-row  form-group   form-detail-field"> 
                                <div class="div-table-col-5" style="text-align:right;"> 
                                     Biaya Lain 
                                </div>      
                                <div class="div-table-col-5"> 
                                        <?php echo $obj->input('text','etcCost',true,'','style="text-align:right;"  onChange="purchaseOrderAssets.calculateTotal()" ','form-control inputnumber'); ?>
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
                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-5" style="text-align:right;"> </div>  
                                <div class="div-table-col-5"> 
                                       <div class="form-detail-button" style="float:right; text-align:right;" relalt="Sembunyikan Detail">Tampilkan Detail</div>
                                </div>
                                <div class="div-table-col"> </div>
                            </div> 
                            
                      </div>    
      				 
      				  <div style="clear:both"></div>
         </div>
         
       <div style="clear:both"></div>
       
        <div class="form-button-panel" > 
       	 <?php if (empty($_GET['id']) || $_POST['selStatus'] == 1) echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>  
      <div class="data-history"> 
      <div class="content">
          <?php
            if (!empty($id)){
                $rs = $obj->generateDataHistory($id); 
                echo $obj->compileDataHistoryForAdminForm($rs);
            }
          ?></div>
    </div>
</div> 
</body>

</html>