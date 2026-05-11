<?php 

include '../../_config.php'; 
include '../../_include.php';  

$obj= $truckingServiceWorkOrder;
$securityObject = $obj->securityObject; 

if(!$security->isAdminLogin($securityObject,10,true)); 
 


?> 
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive-1.0.min.css" /> 

<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script>
jQuery(document).ready(function(){    
       
    var baseURL = '/admin/dashboard/';
    var fetching = {};
    var fetchingIndex = 0;
    var workOrderRowName = 'work-order-detail-row';
    
    function updateWorkProgress(){  
              
         $.ajax({
			type: "POST",
            //data : 'action=updateWorkProgress', 
			url: baseURL + "ajax-work-order.php",  
			success: function(data){    
                if (!data) {
                    // remove all rows
                    $("."+workOrderRowName).remove();
                }
                  
                data = JSON.parse(data);

                for(var i=0;i<data.length;i++){ 
                    
                    if($("#" +data[i]['wokey']).length == 0){  
                        $newRow = $(".row-template").clone().removeClass("row-template").addClass(workOrderRowName); 
                        $newRow.insertAfter($(".row-header")); 
                        $newRow.attr("id", data[i]['wokey']);

                        $newRow.find(".socode").html(data[i]['socode']);
                        $newRow.find(".wocode").html(data[i]['wocode']);
                        $newRow.find(".customername").html(data[i]['customername']);
                        $newRow.find(".registrationnumber").html(data[i]['policenumber']);
                        $newRow.find(".drivername").html(data[i]['drivername']); 
                        //$newRow.find(".route").html(data[i]['routefrom'] + " - " + data[i]['routeto']);
                        $newRow.find("[name=hidTrackerId]").val(data[i]['gpstrackerid']); 
                    }  
                } 
                  
			} 
		}); 
    } 
  
    setInterval(updateWorkProgress, 180000);
    updateWorkProgress();     
    
    
     function updateGPSInformation(){  
               
         var rows = $("." + workOrderRowName); 
         
         if (fetchingIndex >= rows.length) fetchingIndex = 0;
         
         var currentRow = rows.eq(fetchingIndex); 
         var policenumber = currentRow.find(".registrationnumber").html();
          
         policenumber = policenumber.replace(/\s/g,'');
         //console.log(policenumber);
          
         if (fetching[policenumber]) return;
          
          
         $.ajax({
			type: "GET",
            data : 'policenumber='+policenumber, 
            url: baseURL + "ajax-gps-eti.php", 
            beforeSend : function() {
                currentRow.addClass("text-blue-munsell");
                fetching[policenumber] = true ;         
            },
			success: function(data){     
                if (!data) return;
                 
                data = JSON.parse(data); 
                
                if(!data['location']) return;
                
                var objLocation = currentRow.find(".location");
                var objSpeed = currentRow.find(".speed");
                
                var address = data['location']['address'];
                var latitude = data['location']['latitude'];
                var longitude = data['location']['longitude'];
                /*var speed = data['speed']; 
                objSpeed.html(speed);*/
                
                //console.log(objLocation.html() + " != " + location);
                if (address && objLocation.html() != address){ 
                    var el = objLocation,
                    newone = el.clone(true);

                    el.before(newone); 
                    objLocation.remove();

                    newone.html('<a href="../dashboard/maps?location='+latitude+','+longitude+'" target="_blank">'+address+'</a>');    
                } 
   
                    
                var vehicleStatus = '';
                var classLabel = '';
                
                if(data['vehiclestatus']){
                    vehicleStatus = data['vehiclestatus'];
                    
                    switch(vehicleStatus.toLowerCase()) {
                      case 'jalan':
                        classLabel = 'text-green-avocado';
                        break;
                      case 'parkir':
                         classLabel = 'text-red-cardinal';
                        break;
                      default:
                         classLabel = '';
                    } 
                }
                
                    currentRow.find(".vehiclestatus").html("<div class=\""+classLabel+"\">"+vehicleStatus+"</div>");
               
                  
			},
            complete:function(xhr, desc) {    
                currentRow.removeClass("text-blue-munsell");
                fetchingIndex++;
                fetching[policenumber] = false;
             }
		}); 
         
    } 
  
    setInterval(updateGPSInformation, 500);

})  
</script>
</head>
<body> 
<div class="dashboard">
<div style="margin:2em">    
<div style="clear:both;height:1em"></div>    
<div class="div-table transaction-detail" style="width: 100%; font-size:1em">
<div class="div-table-row row-header bg-blue-steel text-white" style="font-family:Palanquin">  
    <div class="div-table-col" style="width: 5px;"></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['jobOrder']); ?></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['WOCode']); ?></div> 
    <div class="div-table-col" style="width: 200px;"><?php echo strtoupper($obj->lang['customer']); ?></div> 
    <div class="div-table-col" style="width: 100px;"><?php echo strtoupper($obj->lang['carRegistrationNumber']); ?></div> 
    <div class="div-table-col" style="width: 150px;"><?php echo strtoupper($obj->lang['driver']); ?></div> 
    <div class="div-table-col" style="width: 130px;"><?php echo strtoupper($obj->lang['status']); ?></div> 
<!--    <div class="div-table-col" style="width: 200px;"><?php echo strtoupper($obj->lang['route']); ?></div> 
    <div class="div-table-col" style="width: 60px; text-align:center">KM / H</div> -->
    <div class="div-table-col"><?php echo strtoupper($obj->lang['location']); ?></div>
    <div class="div-table-col" style="width: 5px;"></div> 
</div>  
</div> 
</div>
    
<div class="div-table-row row-template" >  
    <div class="div-table-col"><?php echo $obj->inputHidden('hidTrackerId'); ?></div> 
    <div class="div-table-col socode"></div> 
    <div class="div-table-col wocode"></div> 
    <div class="div-table-col customername"></div> 
    <div class="div-table-col registrationnumber"></div> 
    <div class="div-table-col drivername"></div>
<!--    <div class="div-table-col route"></div>  
    <div class="div-table-col speed" style=" text-align:center"></div>  -->
    <div class="div-table-col vehiclestatus"></div>
    <div class="div-table-col"><div class="location blink"></div></div>  
    <div class="div-table-col"></div> 
</div> 
</div>        
</body>    
</html>