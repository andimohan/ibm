<?php
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $item;

if (empty($_SESSION[$class->loginAdminSession]['id']))  
    die;
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$id = base64_decode($_SESSION[$class->loginAdminSession]['id']);
 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 

<script type="text/javascript">  

	jQuery(document).ready(function(){  
	 	
        var tabID =  selectedTab.newPanel[0].id;
        setOnDocumentReady(tabID);   
		  
		  
        $( "#" + tabID + " [name=btnSubmit]" ).on('click', function() { 
                
            $("#" +  tabID + " .onmarket").find("input").val("");

            var sn = $( "#" +  tabID + " [name=serialnumber]" ).val();
            if(sn == ""){ 
                alert(phpErrorMsg.serialnumber[1]);
                return;
            }

              $.ajax({  
                        type: "GET", 
                        url:  'ajax-sn.php', 
                        data: "action=getSNInformation&sn="+ sn ,  
                        success: function(data){
                            
                            var data = JSON.parse(data); 
                             
                            if (!data || data.length == 0){ 
                                alert(phpErrorMsg.serialnumber[4]);
                                return;
                            }
                            
                            data = data[0];
                              
                            $( "#" +  tabID + " [name=vendorPartNumber]").val(data.vendorpartnumber); 
                            $( "#" +  tabID + " [name=itemName]").val(decodeHTMLEntities(data.itemname)); 
                            $( "#" +  tabID + " [name=itemCode]").val(data.itemcode); 
                              
                            // ITEM IN
                            var warrantyperiodexpireddate =  (data.warrantyperiodexpireddate != '') ? moment(data.warrantyperiodexpireddate).format("DD / MM / YYYY") : '';
                            $( "#" +  tabID + " [name=warrantyExpiredDate]").val(warrantyperiodexpireddate);
                           
                            var warrantyvendorperiodexpireddate =  (data.warrantyvendorperiodexpireddate != '') ? moment(data.warrantyvendorperiodexpireddate).format("DD / MM / YYYY") : '';
                            $( "#" +  tabID + " [name=warrantyVendorExpiredDate]").val(warrantyvendorperiodexpireddate);
                           
                            $( "#" +  tabID + " [name=itemInCode]").val(data.itemincode);  
                            $( "#" +  tabID + " [name=itemInDate]").val(moment(data.itemindate).format("DD / MM / YYYY"));
                            
                            var invoicedate =  (data.invoicedate != '') ? moment(data.invoicedate).format("DD / MM / YYYY") : '';
                            $( "#" +  tabID + " [name=invoiceDate]").val(invoicedate);
                            
                            $( "#" +  tabID + " [name=supplierName]").val(data.suppliername); 
                          
                             
                            // ITEM OUT
                            $( "#" +  tabID + " [name=itemOutCode]").val(data.itemoutcode);  
                            $( "#" +  tabID + " [name=itemOutDate]").val(moment(data.itemoutdate).format("DD / MM / YYYY"));
                            $( "#" +  tabID + " [name=customerName]").val(data.recipientname);  
                             
                        }  
                    }) ; 
        }); 
         
        
});
	
	 
	  
</script>
</head> 

<body>
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
   <form id="defaultForm" method="post" class="form-horizontal" action="snInformation.php">
        <?php prepareOnLoadDataForm($obj); ?> 
     
     <div class="div-table  main-tab-table-1">
                <div class="div-table-row">
                    <div class="div-table-col">
                     		<div class="div-tab-panel">   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['serialNumber']); ?></label> 
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume"><?php echo $obj->inputText('serialnumber'); ?></div>
                                            <div><?php  echo $obj->inputButton('btnSubmit', $obj->lang['check']); ?></div>
                                        </div> 
                                    </div>  
                                </div>        
                                <div class="onmarket">
                                      
                                <div class="form-group">
                                    <label class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['generalInformation']); ?></label>  
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemCode']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemCode', array('readonly' => true)); ?>
                                    </div> 
                                </div>   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('vendorPartNumber', array('readonly' => true)); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemName', array('readonly' => true)); ?>
                                    </div> 
                                </div>     
                                    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warrantyExpiredDate']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div style="float:left; width:50%">
                                            <div class="flex">
                                              <div ><?php echo ucwords($obj->lang['supplier']); ?></div>
                                              <div class="consume"><?php echo $obj->inputText('warrantyVendorExpiredDate', array('readonly' => true, 'etc' => 'style="text-align:center"')); ?></div>
                                            </div>
                                        </div>
                                        <div style="float:left; width:50%; padding-left:0.5em">
                                            <div class="flex">
                                              <div ><?php echo ucwords($obj->lang['customer']); ?></div>
                                              <div class="consume"><?php echo $obj->inputText('warrantyExpiredDate', array('readonly' => true, 'etc' => 'style="text-align:center"')); ?></div>
                                            </div>
                                        </div> 
                                    </div>  
                                </div> 
                                    
                                <div class="form-group">
                                    <label class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['itemIn']); ?></label>  
                                </div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemInCode', array('readonly' => true)); ?>
                                    </div> 
                                </div>   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceDate']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('invoiceDate', array('readonly' => true)); ?>
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemInDate']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemInDate', array('readonly' => true)); ?>
                                    </div> 
                                </div>   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('supplierName', array('readonly' => true)); ?>
                                    </div> 
                                </div>     
                                <div class="form-group">
                                    <label class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['itemOut']); ?></label>  
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemOutCode', array('readonly' => true)); ?>
                                    </div> 
                                </div>     
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemOutDate']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('itemOutDate', array('readonly' => true)); ?>
                                    </div> 
                                </div>     
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                    </div> 
                                </div>   
                                    
                                <div class="form-group">
                                    <label class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['othersInformation']); ?></label>  
                                </div> 
                                </div>
                           </div> 
                    </div>   
                </div>
        </div>
         
    </form>
</div>
</body>

</html>