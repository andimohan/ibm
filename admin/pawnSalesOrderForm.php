<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $pawnSalesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'pawnSalesOrderList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = '';  
$editContractInactiveCriteria = ''; 
 
$rsSalesDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trDueDate'] = date('d / m / Y');

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['selSalesKey'] = $saleskey;
   
$rs = prepareOnLoadData($obj);  
$daysLateLabel = '';
$daysLate = 0;
$minPercentage = 0.3;
$fine = 0;
$minRedeem = 0;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
    
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
	 
    $_POST['refcode'] = $obj->getRefCode($rs[0]['refkey'], $obj->tableName);
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['trDueDate'] = $obj->formatDBDate($rs[0]['trduedate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];  
	$_POST['loanAmount'] = $obj->formatNumber($rs[0]['loanamount']); 
	$_POST['itemTotalValue'] = $obj->formatNumber($rs[0]['itemtotalvalue']); 
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
	$_POST['selContract'] = $rs[0]['contractkey'];  
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);  
	$_POST['recipientName'] = $rs[0]['recipientname'];
	$_POST['recipientPhone'] = $rs[0]['recipientphone'];
	$_POST['recipientEmail'] = $rs[0]['recipientemail'];
	$_POST['recipientAddress'] = $rs[0]['recipientaddress'];
	$_POST['interest'] = $obj->formatNumber($rs[0]['interest'],2); 
	$_POST['interestvalue'] = $obj->formatNumber($rs[0]['interestvalue']);
	$_POST['fine'] = $obj->formatNumber($rs[0]['fine']);
	$_POST['fineDiscount'] = $obj->formatNumber($rs[0]['finediscount']); 
	$_POST['totalRedeem'] = $obj->formatNumber($rs[0]['totalredeem']);  
	$_POST['minRedeem'] = $obj->formatNumber($rs[0]['minredeem']);  
    
    $daysLate = $obj->formatNumber($rs[0]['dayslate']); 
    $fine = $rs[0]['fineagreed']; 
        
    if ($rs[0]['statuskey'] == 2){ 
        $redeemInformation = $obj->calculateRedeem($id);
        $daysLate = $redeemInformation['daysLate']; 
        
        $loanAmount = $rs[0]['loanamount'];
        $etcCost = $rs[0]['etccost'];
        $fine = $redeemInformation['fine'];
        $totalFine = $redeemInformation['totalFine'];
        $totalInterest = $rs[0]['interestvalue'];
        
        $interestAndFine =  $totalInterest + $totalFine - $rs[0]['finediscount'];
            
        $totalRedeem = $rs[0]['grandtotal'] + $interestAndFine;
        $minRedeem = ( $minPercentage * $loanAmount ) + $etcCost + $interestAndFine; 
        
        $_POST['fine'] = $obj->formatNumber($totalFine);
        $_POST['totalRedeem'] = $obj->formatNumber($totalRedeem);
        $_POST['minRedeem'] = $obj->formatNumber($minRedeem);
    }
        
    $daysLateLabel = ($daysLate > 0) ? '('.$obj->formatNumber($daysLate).' hari)' : '';
        
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editContractInactiveCriteria = 'or '.$contractDuration->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['contractkey']);
  
}


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
}
 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrContract = $obj->convertForCombobox($contractDuration->searchData('','',true, ' and ('.$contractDuration->tableName.'.statuskey = 1 ' .$editContractInactiveCriteria.')'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
    function PawnSalesOrder(tabID) {  
     var duration = 0;    
     var interest = 0;  
     var interesttypekey = 0;
        
     this.updateDetail = function updateDetail(target,objAndValue,ui){

                var detaiLRow = $(target).closest(".transaction-detail-row");

                for(i=0;i<objAndValue.length;i++){   
                    detaiLRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 

                // harus handle manual utk obj autosearch
                detaiLRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']); 

     }
     
     
	this.updatePawnCalculation = function updatePawnCalculation(){  
          var contractkey = $("#" + tabID + " [name='selContract']").val(); 
        
           //update price
             $.ajax({
                type: "GET",
                url:  'ajax-contract-duration.php',
                async: false,
                data: "action=getDataRowById&pkey=" + contractkey ,  
            }).done(function( data ) { 

                duration = 0;
                interest = 0;
                interesttypekey = 0;
                 
                if(data.length == 0)
                    return;

                data = JSON.parse(data) ; 

                if (data.length > 0){  
                    data = data[0];   
                    duration = data.duedays; 
                    interest = data.interest; 
                    interesttypekey = data.interesttypekey;  
                    
                     var trdate = new Date($("#" + tabID + " [name='trDate']").datepicker("getDate"));  
                     var date = new Date();
                     date.setDate(trdate.getDate() + parseInt(duration, 10)); 
                    
                     $("#" + tabID + " [name='trDueDate']").datepicker("setDate", new Date(date.getFullYear(),date.getMonth(),date.getDate()) );  
                     $("#" + tabID + " [name='interest']").val(interest).blur(); 
                    
                    pawnSalesOrder.calculateTotal();
                } 

            });  

    }

	this.calculateDetail = function calculateDetail(obj){     
                    var parentObj =  $(obj).parent().parent();

                    var itemkey =  parentObj.find("[name='hidItemKey[]']").val();
                    if (itemkey == undefined)
                        return;
                        
                    var qty =  unformatCurrency(parentObj.find("[name='qty[]']").val());
                    var priceInUnit =  unformatCurrency(parentObj.find("[name='priceInUnit[]']").val());
          
                    var subtotal = qty * priceInUnit;
                    parentObj.find("[name='subtotal[]']").val(subtotal).blur(); 

                    pawnSalesOrder.calculateTotal();
	       }
	
	this.calculateTotal = function calculateTotal(){  
         
                    var subtotal = 0; 
                    var totalitemvalue = 0; 
                    var loanamount = 0 ;
        
                    $("#" + tabID + " [name='subtotal[]']").each(function() {   
                            totalitemvalue += parseInt(unformatCurrency($(this).val())) || 0;
                    })
 
                    $("#" + tabID + " [name='itemTotalValue']").val(totalitemvalue).blur();
        
                    loanamount = parseInt(unformatCurrency($("#" + tabID + " [name='loanAmount']").val()));
                    if (loanamount == 0){ 
                        loanamount = totalitemvalue;
                        $("#" + tabID + " [name='loanAmount']").val(totalitemvalue).blur();
                    }
                    
                    var etcCost = parseInt(unformatCurrency($("#" + tabID + " [name='etcCost']").val())) || 0 ;  
                    var interest =  parseFloat(unformatCurrency($("#" + tabID + " [name='interest']").val())) || 0 ; 
                    var interestvalue = parseInt(loanamount * interest / 100); 
        
                    var total = loanamount + etcCost;
                    $("#" + tabID + " [name='total']").val(total).blur(); 
                    $("#" + tabID + " [name='interestvalue']").val(interestvalue).blur(); 
            
                    var fine = parseInt(unformatCurrency($("#" + tabID + " [name='fine']").val())) || 0 ;  
                    var fineDiscount = parseInt(unformatCurrency($("#" + tabID + " [name='fineDiscount']").val())) || 0 ;  
                        
                    var totalRedeem = total + interestvalue + fine - fineDiscount;
                    $("#" + tabID + " [name='totalRedeem']").val(totalRedeem).blur(); 
        
                    var minPercentage = <?php echo $minPercentage; ?>;
                   // alert("(" + loanamount + " * " + minPercentage + " ) + etcCost + " + interestvalue + " + " + fine + " - " + fineDiscount);
                    var minRedeem = (loanamount * minPercentage) + etcCost + interestvalue + fine - fineDiscount;
                    $("#" + tabID + " [name='minRedeem']").val(minRedeem).blur(); 
            
                    $("#" + tabID + " .default-min-redeem").html(minRedeem).formatCurrency();
            
                     
                
    }
        
         
    this.updateCustomerInformation =  function updateCustomerInformation(){ 
                      var customerkey = $( "#" + tabID + " [name=hidCustomerKey]" ).val();  
                       
                        if(!customerkey)
                            return;

                       $.ajax({
                            type: "GET",
                            url:  'ajax-customer.php',
                            async: false,
                            data: "action=getDataRowById&pkey=" + customerkey ,  
                        }).done(function( data ) { 
                          
                                data = JSON.parse(data) ; 
                                data = data[0];
                                  
                                var address = "";
                                address = data.address ;
                                if (data.address2 != "")
                                    address += "\n" + data.address2;  

                                $("#" + tabID + " [name=hidCreditLimit]").val(data.creditlimit); 
                             
                                $("#" + tabID + " [name=recipientName]").val(data.name);
                                $("#" + tabID + " [name=recipientPhone]").val(data.phone);
                                $("#" + tabID + " [name=recipientEmail]").val(data.email);
                                $("#" + tabID + " [name=recipientAddress]").val(address);  
 
                        });
                }
    
    this.updateRecipients = function updateRecipients(){
         
                    var recipientName =  "";
                    var recipientPhone = "";
                    var recipientEmail = "";
                    var recipientAddress =  "";  
        
                    recipientName = $( "#" + tabID + " [name=recipientName]" ).val(); 
                    recipientPhone = $( "#" + tabID + " [name=recipientPhone]" ).val(); 
                    recipientEmail = $( "#" + tabID + " [name=recipientEmail]" ).val(); 
                    recipientAddress = $( "#" + tabID + " [name=recipientAddress]" ).val();  
                    

                    pawnSalesOrder.updateCustomerInformation();
                }
     
     
     }
    
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
        pawnSalesOrder = new PawnSalesOrder(tabID);
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
                        }
                    } 
                },
              
			 
            }
        })
        .on('success.form.bv', function(e) {   
               <?php echo $obj->submitFormScript(); ?>  
        });
		 
		 	 
	 	  
 
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
	  	objAndValue.push({object:'priceInUnit[]', value :'sellingprice'});  
	  	objAndValue.push({object:'defaultPrice[]', value :'sellingprice'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValue; 
	  	 	
	     
		// DETAIL CLONE
		 $("#defaultForm-"+tabID+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("detail-row-template");
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&typekey=3','pawnSalesOrder.updateDetail'); 
        });
		 

        $("#" + tabID + " [name=btnSaveEmail]").click(function() {  
            $("#" + tabID + " [name=hidSendEmail]").val(1);
            $("#" + tabID + " #defaultForm").submit();
        }); 
	 
        <?php if (!empty($id)) { ?> 
        $("#" + tabID + " [name=btnClosed]").click(function() { 
            
            $( "#dialog-message" ).html("Anda yakin akan menebus transaksi ini ?");
            $( "#dialog-message" ).dialog({
              width: 300,
              modal: true,
              title:"Konfirmasi Tebus Gadai", 
              open: function() {
                  $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
              },

              close:function() {}, 

              buttons : {
                  OK : function (){
                            $("#" + tabID + " [name='action']").val("closedTransaction");   
                            $.ajax({  
                                type: "POST",
                                url:  'ajax-pawn-sales-order.php',
                                async: false,
                                data:  $("#defaultForm-"+tabID).serialize() ,  
                                success: function(data){  
                                    var temp = JSON.parse(data)[0];   
                                    alert(temp.message) 

                                    selectedTab.newTab[0].remove();
                                    $tabs.tabs("refresh");   

                                    var num_tabs = findTabIndexByTitle("<?php echo $parentTitle; ?>"); 
                                    $tabs.tabs( "option", "active", num_tabs );  

                                    updateData(false,  "<?php echo $parentPanelId; ?>" ); 

                                } 
                            }) ;  

                            $( this ).dialog( "close" );
                  },
                  Cancel : function (){ 
                    $( this ).dialog( "close" );
                  }
              },
            }); 
                
        }); 
        
        $("#" + tabID + " [name=btnSell]").click(function() {  
                    $( "#dialog-message" ).html("Anda yakin akan menjual transaksi ini ?");
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Jual Gadai", 
                      open: function() {
                          $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                      },

                      close:function() {}, 

                      buttons : {
                          OK : function (){
                                    $("#" + tabID + " [name='action']").val("sellTransaction");   
                                    $.ajax({  
                                        type: "POST",
                                        url:  'ajax-pawn-sales-order.php',
                                        async: false,
                                        data:  $("#defaultForm-"+tabID).serialize() ,  
                                        success: function(data){  
                                            var temp = JSON.parse(data)[0];   
                                            alert(temp.message) 

                                            selectedTab.newTab[0].remove();
                                            $tabs.tabs("refresh");   

                                            var num_tabs = findTabIndexByTitle("<?php echo $parentTitle; ?>"); 
                                            $tabs.tabs( "option", "active", num_tabs );  

                                            updateData(false,  "<?php echo $parentPanelId; ?>" ); 

                                        } 
                                    }) ;  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                    }); 
        }); 
        
        $("#" + tabID + " [name=btnCopyNew]").click(function() {  
                    $( "#dialog-message" ).html("Anda yakin akan melakukan gadai ulang transaksi ini ?");
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Gadai Ulang", 
                      open: function() {
                          $(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:last').focus();
                      },

                      close:function() {}, 

                      buttons : {
                          OK : function (){
                                    $("#" + tabID + " [name='action']").val("extendTransaction");   
                                    $.ajax({  
                                        type: "POST",
                                        url:  'ajax-pawn-sales-order.php',
                                        async: false,
                                        data:  $("#defaultForm-"+tabID).serialize() ,  
                                        success: function(data){  
                                            var temp = JSON.parse(data)[0];   
                                            alert(temp.message) 

                                            selectedTab.newTab[0].remove();
                                            $tabs.tabs("refresh");   

                                            var num_tabs = findTabIndexByTitle("<?php echo $parentTitle; ?>"); 
                                            $tabs.tabs( "option", "active", num_tabs );  

                                            updateData(false,  "<?php echo $parentPanelId; ?>" ); 

                                        } 
                                    }) ;  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                    }); 
        }); 
        
        
        
	    <?php } ?>
                
    <?php if (empty($_GET['id'])){ ?> 
        addNewTemplateRow("detail-row-template");
        pawnSalesOrder.updatePawnCalculation(); 
    <?php }  ?>  
         
    bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&typekey=3','pawnSalesOrder.updateDetail');
  
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
                                   <div class="div-table-caption border-orange">Informasi Umum</div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>    
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode Referensi</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refcode', array('readonly' => true)); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Cabang</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Jenis Kontrak</label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputSelect('selContract', $arrContract, array('etc' => 'onChange="pawnSalesOrder.updatePawnCalculation()"')); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Tgl. Transaksi</label> 
                                        <div class="col-xs-3"> 
                                            <?php echo $obj->inputDate('trDate', array('etc' => 'style="text-align:center"')); ?> 
                                        </div> 
                                        <label class="col-xs-3 control-label" style="text-align:right;">Tgl. Jatuh Tempo</label> 
                                        <div class="col-xs-3"> 
                                            <?php echo $obj->inputDate('trDueDate', array('disabled' => true, 'etc' => 'style="text-align:center"')); ?> 
                                        </div> 
                                    </div>      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Pelanggan</label> 
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
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => $obj->lang['add'] . ' - ' . $obj->lang['customer']
                                                                                                ),
                                                                                'callbackFunction' => 'pawnSalesOrder.updateRecipients()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>         
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Catatan</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>            
                                    <?php 
                                        if (!empty($rs) && $rs[0]['statuskey'] == 3) { 
                                            $rsStatus = $obj->getStatusById($rs[0]['closingtype'],$obj->tableClosingType);
        
                                            switch($rs[0]['closingtype']){
                                                case '1' : $bgclass = 'bg-green-avocado border-green-avocado';
                                                            break;
                                                case '2' : $bgclass = 'bg-red-cardinal border-red-cardinal';
                                                            break;
                                                case '3' : $bgclass = 'bg-princeton-orange border-princeton-orange';
                                                            break;
                                                    
                                                default : $bgclass = '';
                                            }
                                    ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Alasan Penutupan</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('txtClosingType', array('value' => strtoupper($rsStatus[0]['status']),'class' => 'form-control text-white ' . $bgclass)); ?>
                                        </div> 
                                    </div>    
                                    <?php } ?>
                                 
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue">Informasi Pelanggan</div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Nama</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientName', array('readonly' => true)); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Telepon</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone', array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Email</label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail', array('readonly' => true)); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label">Alamat</label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?> 
                                </div> 
                            </div>  
                        </div>  
                    </div>
           </div>
      </div> 
      
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header">Nama Barang</div>
                    <div class="div-table-col detail-col-header" style="width:200px; ">Catatan</div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;">Jumlah</div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Nilai Gadai @</div> 
                    <div class="div-table-col detail-col-header" style="width:100px;"></div> 
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;">Subtotal</div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>"  style="width:70px"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                            $unitname = 'Pcs';
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber'; 

                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['trDetailDesc[]'] = $rsSalesDetail[$i]['trdesc']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']);  
                            $_POST['defaultPrice[]'] =   $obj->formatNumber($rsSalesDetail[$i]['defaultpriceinunit']);  
                            $_POST['subtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']);  
                            
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => ' onChange="pawnSalesOrder.calculateDetail(this)" ' . $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trDetailDesc[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('value' => 1, 'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" onChange="pawnSalesOrder.calculateDetail(this)" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" onChange="pawnSalesOrder.calculateDetail(this)" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('defaultPrice[]', array('readonly' => true,'overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('subtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , $obj->lang['delete'], array('class' => 'btn btn-link remove-button', 'etc' => 'attrhandler="pawnSalesOrder.calculateTotal()"')); ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
       
          <div> 
              <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:72px; height: 1em"></div>  
              
              <div class="div-table" style="float:right; padding-left:1em"> 
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Bunga 
                        </div>  
                        <div class="div-table-col-5"> 
                            <div style="width:96px; float: right;"><?php echo $obj->inputNumber('interestvalue', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?></div>
                            <div style="width:20px; float: right; text-align:center; line-height: 3em">%</div>
                            <div style="width:50px; float: right;"><?php echo $obj->inputDecimal('interest', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?></div>
                         </div>
                        <div class="div-table-col"> </div>
                    </div>    
                    <?php if (!empty($rs) &&  $rs[0]['statuskey'] <> 1) { ?> 
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Denda <?php echo $daysLateLabel; ?>
                                <div class="text-muted" style="font-style:italic">Rp. <?php echo $obj->formatNumber($fine); ?></div> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputDecimal('fine', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?> 
                         </div>
                        <div class="div-table-col"> </div>
                    </div>   

                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Diskon Denda 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputNumber('fineDiscount', array(  'etc' => 'style="text-align:right;" onChange="pawnSalesOrder.calculateTotal()"', 'allowedStatusForEdit' => array(1,2))); ?> 
                         </div>
                        <div class="div-table-col"> </div>
                    </div>  

                   <div class="div-table-row"> 
                      <div class="div-table-col-5"> </div>
                      <div class="div-table-col-5"> </div>
                    </div>

                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Total Pelunasan
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputNumber('totalRedeem', array('readonly' =>true,  'etc' => 'style="text-align:right;"')); ?> 
                         </div>
                        <div class="div-table-col"> </div>
                    </div>  
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Min. Pelunasan (30%)
                               <div class="text-muted" style="font-style:italic">Rp. <span class="default-min-redeem"><?php echo $obj->formatNumber($minRedeem); ?></span></div> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputNumber('minRedeem', array('etc' => 'style="text-align:right;"', 'allowedStatusForEdit' => array(1,2))); ?> 
                         </div>
                        <div class="div-table-col"> </div>
                    </div>  
                    <?php } ?> 
                </div>    

              <div class="div-table" style="float:right;">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            Nilai Barang 
                        </div>  
                        <div class="div-table-col-5" style="width:180px;"> 
                            <?php echo $obj->inputNumber('itemTotalValue', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>

                    </div>       
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            Pokok Hutang 
                        </div>  
                        <div class="div-table-col-5" style="width:180px;"> 
                            <?php echo $obj->inputNumber('loanAmount', array ('etc' => 'style="text-align:right;"  onChange="pawnSalesOrder.calculateTotal()"')) ;?>   
                        </div>

                    </div>       
                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                             Biaya Lain 
                        </div>      
                        <div class="div-table-col-5"> 
                            <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;" onChange="pawnSalesOrder.calculateTotal()" ')); ?> 
                          </div>
                        <div class="div-table-col" > </div>
                    </div>
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                                Total 
                        </div>  
                        <div class="div-table-col-5"> 
                            <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                        <div class="div-table-col"> </div>
                    </div>  
 

                </div>    
              <div style="clear:both"></div> 
        </div>
         
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(); echo ' ' ; ?>
         <?php 
				if($security->isAdminLogin($obj->securityObject,11,false)) {
					$totalSent =  (isset($rs[0]['invoicesent'])) ? $rs[0]['invoicesent'] : 0; 
					echo $obj->inputSubmit('btnSaveEmail','Simpan & Email ('.$totalSent.')', array('etc' => 'style="margin-left:0.5em"', 'allowedStatusForEdit' => array(1)));
				}
		?> 
        <?php  
             if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
                echo  $obj->inputButton('btnClosed','Tebus', array ('etc' => ' style="margin:0 0.3em" ', 'class' => 'btn btn-primary bg-green-avocado border-green-avocado', 'allowedStatusForEdit' => array(2)));   
                echo  $obj->inputButton('btnSell','Jual', array ('etc' => ' style="margin:0 0.3em" ', 'class' => 'btn btn-primary bg-red-cardinal border-red-cardinal', 'allowedStatusForEdit' => array(2)));   
                echo  $obj->inputButton('btnCopyNew','Perpanjang', array ('class' => 'btn btn-primary bg-princeton-orange border-princeton-orange', 'etc' => ' style="margin:0 0.3em" ','allowedStatusForEdit' => array(2)));   
                echo  $obj->inputSubmit('btnEmail','Kirim Ulang Email ('.$rs[0]['invoicesent'].')', array ('etc' => ' style="margin:0 0.3em" ', 'allowedStatusForEdit' => array(2)));  
             }
         ?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
