<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('TruckingServiceWorkOrder.class.php', 'Employee.class.php', 'Car.class.php'));

$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$warehouse = new Warehouse();

$obj = $truckingServiceWorkOrder;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y');
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1)'),'pkey','name'); 

?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>pace-theme-center-simple.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive-1.0.min.css" /> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui-timepicker-addon.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.contextMenu-1.1.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>clock.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>moment.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.111.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?><?php echo ADMIN_JS_VERSION; ?>"></script> 

<title><?php echo $obj->lang['truckingServiceWorkOrder'] ?></title> 

<style>
	.detail-col-detail {vertical-align: top}
    .gps-location {text-align:center;position:relative; top: 1em}
</style>


<script type="text/javascript">
   const _DATE_TIME_FORMAT_ = 'HH:mm';

  var objAndValueForDriverDetailAutoComplete = [];
  var objAndValueForCarDetailAutoComplete = [];

  objAndValue = new Array;
  objAndValue.push({object:'hidDriverKey[]', value :'pkey'});  
  objAndValueForDriverDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidCarKey[]', value :'pkey'});  
  objAndValueForCarDetailAutoComplete = objAndValue; 

  var workOrderDetailRow = 'transaction-detail-row';

	 function rebindEl(){
		bindAutoCompleteForTransactionDetail('driverName[]',objAndValueForDriverDetailAutoComplete,'../ajax-employee.php?action=searchData&isdriver=1&limit=25');  
		bindAutoCompleteForTransactionDetail('carName[]',objAndValueForCarDetailAutoComplete,'../ajax-car.php?action=searchData&searchField=code,policenumber&limit=25');   
	 }
	
      function importData() {
         
         var trStartDate = convertDateToStandartFormat($("[name=trStartDate]").val());
         var trEndDate = convertDateToStandartFormat($("[name=trEndDate]").val());
         var warehouseKey = $("[name=selWarehouseKey]").val() || 0;
         
         //console.log(trStartDate);

         var ajaxData = "action=getData&startdate="+trStartDate+"&enddate="+trEndDate+"&warehousekey="+warehouseKey;

         $.ajax({
            type: "GET",
            async: true,
            url: "ajax-trucking-service-work-order.php",
            data: ajaxData, 
              beforeSend:function (xhr){    
                    $(".gps-location").hide();
	            }, 
            success: function(res) {
               if(!res) return;
               var data = parseJSON(res);  
    
               $(".transaction-detail-row").remove();
               for(var i=0; i < data.length; i++) {
                  $newRow = $(".row-template").clone().removeClass("row-template").addClass(workOrderDetailRow); 
                  $newRow.insertBefore($(".row-template"));
 
			   	  updateRowValue($newRow,data[i]);  
               }

                rebindEl();
				$( "[name=btnSave]" ).on( "click", function() {   updateData($(this).closest('.transaction-row'));});
				 
		 	   disabledButton($("[name=btnImport]"),false);   
            }
         });
      }

	   
      function reUpdateDataRow($row){
         var wokey = $row.find("[name='hidWorkOrderKey[]']").val();
		  
         $.ajax({
            type: "GET",
            async: false,
            url: "ajax-trucking-service-work-order.php",
            data: "action=getDataRowById&pkey="+wokey,
            success: function(res) {
				
               if(!res) return;
               var data = parseJSON(res);  
               data = data[0];
				
			   updateRowValue($row,data); 
            }
         });
      }
	   
	  function updateRowValue(row,data){
		    
		    var policenumber = (data.carkey !== null) ? data.policecode + ' - ' + data.policenumber : '';
		  
		    row.find("[name='JOCode[]']").val(data.serviceordercode);
			row.find("[name='workOrderCode[]']").val(data.code);
			row.find("[name='hidWorkOrderKey[]']").val(data.pkey);
			row.find("[name='customerName[]']").val(decodeHTMLEntities(data.customername));
			row.find("[name='consigneeName[]']").val(decodeHTMLEntities(data.consigneename));
			row.find("[name='driverName[]']").val(data.drivername);
			row.find("[name='hidDriverKey[]']").val(data.driverkey);
			row.find("[name='carName[]']").val(policenumber);
			row.find("[name='hidCarKey[]']").val(data.carkey);
			row.find("[name='trDesc[]']").val(data.trdesc);
			row.find("[name='depotName[]']").val(data.depotname);
			row.find("[name='jobTypeName[]']").val(data.jobtypename);
			row.find("[name='serviceName[]']").val(data.servicename);
		  
            if (policenumber != ''){ 
//                row.find(".gps-location").find('a').attr("href","/demo/index.php?registrationNumber=" + data.policenumber.replace(/ /g, ""));
                row.find(".gps-location").find('a').attr("href","/demo/index.php?carkey=" + data.carkey);
                
                row.find(".gps-location").show(); 
            }
          
		    if (typeof data.modifiedon !== 'undefined')
				row.find("[name='lastUpdate[]']").val(moment(data.modifiedon).format(_DATE_TIME_FORMAT_));

	  }

      function updateData($row) {

         var ajaxData = {
            workOrderCode: $row.find("[name='workOrderCode[]']").val(),
            hidWorkOrderKey: $row.find("[name='hidWorkOrderKey[]']").val(),
            hidCarKey: $row.find("[name='hidCarKey[]']").val(),
            hidDriverKey: $row.find("[name='hidDriverKey[]']").val(),
            trDesc: $row.find("[name='trDesc[]']").val() 
         };

         $.ajax({
            type: "POST",
            async: false,
            url: "ajax-trucking-service-work-order.php",
            data: {
               action: "updateWorkOrder",
               data: ajaxData
            },
            success: function(response) {

               if(!response) return;
               var data = parseJSON(response);
               var result = data[0];
               if(result.valid) {
                  alert(result.message);
                  reUpdateDataRow($row);
               } else {
                  alert(result.message);
               }
               
            }, 
            error: function(xhr, status, error) {
               console.error('Error updating data:', error);
            },
            complete:function(xhr, desc) { 
               rebindEl();
            }
         });
      }
 

      function parseJSON(data){ 

         data = $.trim(data);

         if(!data) data = '[]'; 
         if(data.length == 0) data = '[]'; 

         return JSON.parse(data);
      }

	
   jQuery(document).ready(function(){  

	   $( "[name=btnImport]" ).on( "click", function() { 
		 	 disabledButton($(this));   
		     importData();  
	  });
	    
 
	   $( "[name=btnImport]" ).click();

  
   });
   

</script>

</head>
   <body> 
   <div class="dashboard">
      <div style="margin:2em">    
		  
	 <h1>WORK ORDER SHEET</h1>
		  
      <div style="width:100%; margin:auto; " class="tab-panel-form">
      <div class="notification-msg"></div>
		<div class="flex">
		  <div style="font-size:1.5em; padding-right:1em"><?php echo ucwords($obj->lang['period']); ?></div>
		  <div><?php echo $obj->inputDate('trStartDate', array('etc' => 'style="text-align:center"')); ?></div>
		  <div>-</div>
		  <div><?php echo $obj->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); ?></div>
		  <div><?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?></div>
		  <div> <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'], array('class' => 'btn btn-primary btn-second-tone')); ?> </div>
	   </div>  	 
        <div style="clear:both; height: 1em"></div> 
      <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
         
         <div class="div-table-row"> 
            <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['JOCode']) .' / '. ucwords($obj->lang['WOCode']); ?></div>
            <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['jobType']); ?></div>
            <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['customer']) . ' / '.ucwords($obj->lang['consignee']); ?></div> 
            <div class="div-table-col detail-col-header"  ><?php echo ucwords($obj->lang['note']); ?></div>
            <div class="div-table-col detail-col-header" style="width:150px;"  ><?php echo ucwords($obj->lang['depot']); ?></div>
            <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['car']). ' / '.ucwords($obj->lang['driver']); ?></div> 
            <div class="div-table-col detail-col-header" style="width:70px; text-align:center"><?php echo ucwords($obj->lang['update']); ?></div>
            <div class="div-table-col detail-col-header" style="width:60px;"></div>
         </div>

        

         <div class="div-table-row row-template transaction-row">
   
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText('JOCode[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' )); ?>
               <?php echo $obj->inputHidden('hidWorkOrderKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
               <?php echo $obj->inputText('workOrderCode[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' , 'etc'=>'style="margin-top:0.2em"' )); ?>
            </div>
            <div class="div-table-col detail-col-detail">
				 <?php echo $obj->inputText('serviceName[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' )); ?>
				 <?php echo $obj->inputText('jobTypeName[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' , 'etc'=>'style="margin-top:0.2em"' )); ?>
            </div>
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText('customerName[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' )); ?>
				<?php echo $obj->inputText('consigneeName[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control', 'etc'=>'style="margin-top:0.2em"' )); ?>
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo  $obj->inputTextArea('trDesc[]', array('overwritePost' => true, 'class' => 'form-control', 'etc' => 'style="height:5em;"')); ?>
            </div>
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText('depotName[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control' )); ?>
            </div>
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText('carName[]', array('overwritePost' => true, 'class' => 'form-control')); ?>
               <?php echo $obj->inputHidden('hidCarKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
				
               <?php echo $obj->inputText('driverName[]', array('overwritePost' => true,  'class' => 'form-control', 'etc'=>'style="margin-top:0.2em"' )); ?>
               <?php echo $obj->inputHidden('hidDriverKey[]', array('overwritePost' => true, 'disabled' => '')); ?>
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText('lastUpdate[]', array('overwritePost' => true, 'readonly' => true, 'class' => 'form-control label-style', 'etc' => 'style="text-align:center"')); ?>
               <div class="gps-location"><a href="#" target="_blank"><?php echo $obj->lang['location']; ?></a></div>    
            </div>
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputButton('btnSave', $obj->lang['save'], array('class' => 'btn btn-primary btn-second-tone', 'etc'=>'style="min-width:0"')); ?>
            </div>
         </div>
         

      </div>

   </div>
	   </div>
	</div> 
   </body>    
   <script type="text/javascript">
 
         $(" .input-date" ).datepicker({ 
                                    showButtonPanel: true, 
                                    currentText: 'Now', 
                                    dateFormat:'dd / mm / yy', 
                                    changeMonth: true,  
                                    changeYear: true,
                                    beforeShow : function(input, inst) {  
                                          inst.dpDiv.removeClass('month-year-datepicker');
                                       }
                                    });

                                    
   </script>
</html>