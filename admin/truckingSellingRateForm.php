<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingSellingRate.class.php');
$truckingSellingRate = createObjAndAddToCol(new TruckingSellingRate());
$customer = createObjAndAddToCol(new Customer());
$consignee = createObjAndAddToCol(new Consignee());
$location = createObjAndAddToCol(new Location());
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());
$warehouse = createObjAndAddToCol(new Warehouse());
    
$obj= $truckingSellingRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'truckingSellingRateList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = '';
$rs = prepareOnLoadData($obj); 
$rsDetail = array(); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    
    $_POST['name'] = $rs[0]['name']; 
	$_POST['selCargoType'] = $rs[0]['cargotypekey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];  
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
    
	$_POST['hidCustomerKey'] = $rs[0]['customerkey'] ;  
	if (!empty($rs[0]['customerkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
    }
    
    $_POST['hidConsigneeKey'] = $rs[0]['consigneekey']; 
	if (!empty($rs[0]['consigneekey'])){
		$rsConsignee = $consignee->searchData($consignee->tableName.'.pkey',$rs[0]['consigneekey'],true);
		$_POST['consigneeName'] = $rsConsignee[0]['name'];
        $_POST['warehouseName'] = $rsConsignee[0]['warehousename'];
        $_POST['contactPerson'] = $rsConsignee[0]['contactperson'];
        $_POST['address'] = $rsConsignee[0]['address'];
        
        if (!empty($rsConsignee[0]['locationkey'])){
            $rsLocation = $location->getDataRowById($rsConsignee[0]['locationkey']);
            $_POST['locationName'] = $rsLocation[0]['name'];
        }
	}
    
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
	if (!empty($rs[0]['categorykey'])){
		$rsCategory = $truckingServiceOrderCategory->searchData($truckingServiceOrderCategory->tableName.'.pkey',$rs[0]['categorykey'],true);
		$_POST['categoryName'] = $rsCategory[0]['name']; 
	}
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');  
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
    function TruckingSellingRate(tabID) {  
        
         this.updateConsigneeDetail = function updateConsigneeDetail(){ 
				    
            $("#" + tabID + " [name=warehouseName]").val("");
            $("#" + tabID + " [name=locationName]").val("");
            $("#" + tabID + " [name=contactPerson]").val("");    
            $("#" + tabID + " [name=address]").val("");    
    
            var consigneekey = $( "#" + tabID + " [name=hidConsigneeKey]" ).val();  
         
            if(!consigneekey)
                return;
        
              $.ajax({
                    type: "GET",
                    url:  'ajax-consignee.php',
                    async: true,
                    data: "action=getDataRowById&pkey=" + consigneekey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ;  
                        if (data.length != 0){   
                            data = data[0];  
                              
                            $("#" + tabID + " [name=warehouseName]").val(data.warehousename);
                            $("#" + tabID + " [name=locationName]").val(data.locationname);
                            $("#" + tabID + " [name=contactPerson]").val(data.contactperson);  
                            $("#" + tabID + " [name=address]").val(data.address);  
                        } 
 
                });
         } 
         
         /*
         this.updateProgressQty = function updateProgressQty(){
             
            var categorykey = $( "#" + tabID + " [name=hidCategoryKey]" ).val();  
             
            $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-service-order-category.php',
                    async: false,
                    data: "action=getProgress&categorykey=" + categorykey ,  
                }).done(function( data ) { 
                 
                        if (data != "")
                             data = JSON.parse(data) ;   
                        
                        var totalStep = 1,  totalList = 0, additionalList = 0;
                
                        if (data.length > 1)
                            totalStep = data.length;
                
                        $("#" + tabID + " .transaction-detail-row,#" + tabID + " .detail-row-template").each(function() {
                            $ul = $(this).find(".step-cost");
                            totalList = $ul.length; 
                            additionalList = totalStep - totalList;
                             
                            if (additionalList > 0 ){
                                for(i=0;i<additionalList;i++)
                                    $newRow = $ul.first().clone().insertAfter($ul.last()).show();   
                                    $newRow.find('.inputnumber').bind( "blur", function(event) { inputNumberOnBlur($(this)); });
                            }else if (additionalList < 0 ){ 
                                additionalList = Math.abs(additionalList);
                                for(i=0;i<additionalList;i++){ 
                                    $(this).find(".step-cost").last().remove(); 
                                }
                            } 
		
                        });
                
                        var ctr, name, title;
                        $("#" + tabID + " .transaction-detail-row,#" + tabID + " .detail-row-template").each(function() {
                            $ul = $(this).find(".step-cost");
                            
                            ctr = 0;
                            
                            $($ul).each(function() {
                                 
                                 name = 'Step #' + (ctr + 1);
                                
                                 if (data[ctr])  name = data[ctr].name; 
                                 $(this).find("[name='hidProgressKey[]']").val(data[ctr].progresskey);
                                 $(this).find(".step-name").html(name.toUpperCase());
                                 ctr++;
                            })   
                             
                            $(this).find("[name='hidTotalSteps[]']").val(ctr);
                            
                        });
 
                });
         }
            */
     }
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingSellingRate = new TruckingSellingRate(tabID);
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
              
			   categoryName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.category[1]
                        }
                    } 
                },
              
			 
            }
        })
        .on('success.form.bv', function(e) {
              <?php echo $obj->submitFormScript(); ?>
        }); 
        
		objAndValueContainer = new Array;
		objAndValueContainer.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValueContainer; 
	  	 	
	     // DETAIL CLONE
		 $("#defaultForm-"+tabID+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("detail-row-template"); 
            bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0'); 
        });
                
        <?php if (empty($_GET['id'])){ ?> 
            addNewTemplateRow("detail-row-template");	
        <?php } ?>
 
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0'); 
     
});
	 
     

</script>
 
</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" > 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                    </div>    
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cargoType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selCargoType', $arrCargoType); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $truckingServiceOrderCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-service-order-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php    
                                                    echo $obj->inputAutoComplete(array(
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
                                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                        ) 
                                                                                      )
                                                                                );  
                                            ?> 
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
                                    <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['stuffingInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $consignee, 
                                                                                'element' => array('value' => 'consigneeName',
                                                                                                   'key' => 'hidConsigneeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-consignee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'consigneeForm.php',
                                                                                                    'element' => array('value' => 'consigneeName',
                                                                                                           'key' => 'hidConsigneeKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['consignee'])
                                                                                                ),
                                                                                'callbackFunction' => 'truckingSellingRate.updateConsigneeDetail()',
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consigneeWarehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('warehouseName', array('readonly' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> <?php echo $obj->inputText('locationName', array('readonly' => true)); ?> </div>
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['contactPerson']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('contactPerson', array('readonly' => true)); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('address', array('etc' => 'style="height:10em;"', 'readonly' => true)); ?> 
                                        </div> 
                                    </div> 
                        </div>
                               
                    </div>
           </div>
      </div> 
      
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['services']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right"><?php echo ucwords($obj->lang['sellingPrice']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:45px;"></div> 
                </div>
                
				<?php 
                    
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=count($rsDetail); $i++){   
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname'];    
                            $_POST['price[]'] =   $obj->formatNumber($rsDetail[$i]['price']);   
                        }
                ?>
            
                
                <div class="div-table-row  <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <?php  
                            echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                            echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                            
                        ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                
                    </div> 
                    <div class="div-table-col detail-col-detail" >
                        <?php  
                            echo $obj->inputNumber('price[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc));  
                        ?>
                    </div>  
                    <div class="div-table-col detail-col-detail" style="vertical-align:top">
                        <?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1" ','class' => 'btn btn-link remove-button')); ?>
                    </div>
               </div> 
            
            <?php } ?> 
                   
         </div>        
          
          <div class="asterix-label" style="clear:both; margin-top:1em;" ><span class="asterix">*</span> Harga <i>Fixed</i>.</div> 
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', ucwords($obj->lang['addRows']), array('class' => 'btn btn-primary btn-second-tone')); ?></div> 
        
        <div class="form-button-margin"></div> 
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();  ?>  
        </div> 
        
    </form>   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
