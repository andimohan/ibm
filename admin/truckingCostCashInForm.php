<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj=  $truckingCostCashIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'truckingCostCashInList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsTruckingCost = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsTruckingCost = $obj->getDetailWithRelatedInformation($id); 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['total'] = $obj->formatNumber($rs[0]['total']);
    
	$rsService = $truckingServiceWorkOrder->getDataRowById($rs[0]['refkey']); 
    $_POST['hidWorkOrderKey'] = $rsService[0]['pkey'];  
    $_POST['workOrderNumber'] = $rsService[0]['code'];
    
    
	$rsJO = $truckingServiceOrder->getDataRowById($rsService[0]['refkey']); 
    $_POST['jobOrderCode'] = $rsJO[0]['code'];
    
    $_POST['hidPlannerKey'] = $rs[0]['plannerkey']; 
	if (!empty($rs[0]['plannerkey'])){
		$rsPlanner = $employee->getDataRowById($rs[0]['plannerkey']);
		$_POST['plannerName'] = $rsPlanner[0]['name'];
	}  
    
    $_POST['hidDriverKey'] = $rs[0]['driverkey']; 
	if (!empty($rs[0]['driverkey'])){
		$rsDriver = $employee->getDataRowById($rs[0]['driverkey']);
		$_POST['driverName'] = $rsDriver[0]['name'];
	} 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
  
    function TruckingCostCashIn(tabID){
   
        this.calculateTotal = function calculateTotal(){
            
            var amount = 0;   
             
            $("#" + tabID + " [name='amount[]']").each(function(){  amount += parseInt(unformatCurrency($(this).val())) || 0;  })      
            $("#" + tabID + " [name='total']").val(amount).blur();  
 
        }
        
        this.importData = function importData(){  
           
                var importButton =  $( "#" + tabID + " [name=btnImport]" ); 
            
                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                var activeAjaxConnections = 0;   

                $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-service-work-order.php',
                    beforeSend:function (xhr){
                        importButton.prop('disabled', true) ;     
                        clearAllRows($("#defaultForm-"+tabID));
                        activeAjaxConnections++; 
                    },
                    data: "action=getUnCashedCostDetail&pkey=" +  $( "#" + tabID + " [name=hidWorkOrderKey]" ).val() ,  
                    success: function(data){ 
                            if(data){
                                var data = JSON.parse(data);  
                                var i;

                                for(i=0;i<data.length;i++){   
                                        var arrPostValue = []; 
                                        arrPostValue.push({"selector":"refheadercostkey", "value":data[i].pkey});
                                        arrPostValue.push({"selector":"hidCostKey", "value":data[i].costkey}); 
                                        arrPostValue.push({"selector":"costName", "value":data[i].name}); 
                                        arrPostValue.push({"selector":"amount", "value":data[i].requestamount}); 
                                        arrPostValue.push({"selector":"detailDesc", "value":data[i].description}); 
                                        $newRow = addNewTemplateRow("detail-row-template",JSON.stringify(arrPostValue));    
                                       // bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData','itemAdj.updateDetail'); 
                                      
                               }

                                bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete[tabID],'ajax-coa.php?action=searchData&iscashbank=1');
                                
                                 // make sure the adjustment counted by default, just want to make it easy, so we use .inputnumber
                                 $("#" + tabID + " .inputnumber").change().blur();
                            }
                            

                            activeAjaxConnections--; 
                            if (0 == activeAjaxConnections) 
                                hideOverlayScreen();

                            importButton.prop('disabled', false) ;   
                    } ,
                    error: function(xhr, errDesc, exception) {
                        activeAjaxConnections--; 
                        if (0 == activeAjaxConnections) 
                            hideOverlayScreen();

                        importButton.prop('disabled', false) ;   
                    }
                });

        }
        
        this.updateAccount = function updateAccount(){
            var costkey = $("#" + tabID + " [name=hidCostKey]").val();
            $("#" + tabID + " [name=hidCOAKey]").val("");
            $.ajax({
                    type: "GET",
                    url:  'ajax-item.php',
                    async: false,
                    data: "action=searchData&pkey=" + costkey,  
                }).done(function( data ) {  
                        data = JSON.parse(data);
                        if (data.length > 0){   
                            data = data[0]; 
                            $("#" + tabID + " [name=hidCOAKey]").val(data.costcoakey);
                        } 
                });   
            
        }
        
        
         
    }
    	  
    
	jQuery(document).ready(function(){  
	 	    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingCostCashIn = new TruckingCostCashIn(tabID);
 
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
                workOrderNumber: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.truckingServiceWorkOrder[1]
                        }, 
                    }
				}, 	  
            }
        })
        .on('success.form.bv', function(e) { 
               <?php echo $obj->submitFormScript(); ?> 
        });
		
        $( "#" + tabID + " [name=btnImport]" ).on('click', function() {  
               truckingCostCashIn.importData();  
        });
        
		
		objAndValue = new Array;
		objAndValue.push({object:'hidCOAKey[]', value :'pkey'}); 
        objAndValueForDetailAutoComplete[tabID] = objAndValue;
	  	 /*	
	 	// DETAIL CLONE 
		 $("#"+tabID+"  [name=btnAddRows]").on('click', function() { 
          	addNewTemplateRow("detail-row-template");
			bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1'); 
        });
		 
        <?php if (empty($_GET['id'])){ ?> 
         	addNewTemplateRow("detail-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');
        */
        <?php if (empty($_GET['id'])){ ?> 
         	addNewTemplateRow("detail-row-template");
        <?php } ?>
        
        bindAutoCompleteForTransactionDetail('COAName[]',objAndValueForDetailAutoComplete[tabID],'ajax-coa.php?action=searchData&iscashbank=1');
 
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
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['WOCode']; ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex">
                                    <div  class="consume">
                                          <?php     
                                               echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $truckingServiceWorkOrder,
                                                                                        'revalidateField' => true, 
                                                                                        'element' => array('value' => 'workOrderNumber',
                                                                                                           'key' => 'hidWorkOrderKey'),
                                                                                        'source' => array(
                                                                                                            'url' => 'ajax-trucking-service-work-order.php',
                                                                                                            'data' => array(  'action' =>'searchData', 'statuskey' => '2' )
                                                                                                        )  
                                                                                      )
                                                                                );  


                                            ?> 
                                    </div>
                                    <div><?php echo $obj->inputButton('btnImport',$obj->lang['update'],array( 'class' => 'btn btn-primary semi-fixed btn-second-tone')); ?></div>    
                                </div> 
                            </div>  
                        </div> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrderCode']; ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputText('jobOrderCode', array('readonly' => true)); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['planner']; ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
                                                                                'element' => array('value' => 'plannerName',
                                                                                                   'key' => 'hidPlannerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )  
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'driverName',
                                                                                                   'key' => 'hidDriverKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )  
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                        
                        
                        
                    </div>
                </div>
                
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['cost']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:250px;"><?php echo ucwords($obj->lang['cashBankAccount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['note']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
                    <!-- <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>  -->
                </div>
                
				<?php 
                            
                    $totalRows = count($rsTruckingCost);
                    for ($i=0;$i<=$totalRows; $i++){  
                                
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else { 
                            $_POST['hidDetailKey[]'] =  $rsTruckingCost[$i]['pkey'];  
                            
                            $_POST['hidCostKey[]'] = $rsTruckingCost[$i]['costkey'];
                            $_POST['costName[]'] =  $rsTruckingCost[$i]['costname']; 
                            $_POST['hidCOAKey[]'] = $rsTruckingCost[$i]['coakey'];
                            $_POST['COAName[]'] =  $rsTruckingCost[$i]['coaname'];  
                            $_POST['amount[]'] =   $obj->formatNumber($rsTruckingCost[$i]['price']);  
                            $_POST['detailDesc[]'] =  $rsTruckingCost[$i]['description'];  
                            $_POST['refheadercostkey[]'] = $rsTruckingCost[$i]['refheadercostkey'];  
                        }
                    ?>
            
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('refheadercostkey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'readonly' => true )); ?><?php echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite, 'etc' => $etc )); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailDesc[]',array('overwritePost' => $overwrite, 'etc' => $etc )); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right" onChange="truckingCostCashIn.calculateTotal()" ' .$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>" style="display:none;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="truckingCostCashIn.calculateTotal()"')); ?></div> 
                </div>
                         
                <?php  } ?>   
                   
         </div>        
          <!--
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
         -->
        <div>   
           <!-- <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  -->
            <div class="div-table" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
            </div>   
        </div>          
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
