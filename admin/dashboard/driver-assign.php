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
    .row-header {width: 8em}
    .barcode-input {font-size: 1.6em; width: 100%; max-width: 15em}
    .barcode-input input {font-size: 2.2em; height: 1.3em; text-align: center !important}
    .detail-info-wo { font-size: 1.3em}
</style>


<script type="text/javascript">
   const _DATE_TIME_FORMAT_ = 'DD / MM / YYYY';

  var objAndValueForDriverDetailAutoComplete = [];
  var objAndValueForCarDetailAutoComplete = [];

  objAndValue = new Array;
  objAndValue.push({object:'hidDriverKey[]', value :'pkey'});  
  objAndValueForDriverDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidCarKey[]', value :'pkey'});  
  objAndValueForCarDetailAutoComplete = objAndValue; 

  var workOrderDetailRow = 'transaction-detail-row';
 
    function parseJSON(data){ 

         data = $.trim(data);

         if(!data) data = '[]'; 
         if(data.length == 0) data = '[]'; 

         return JSON.parse(data);
    }

    function updateData() {

         var code = $("[name=code]").val().trim();
         var car = $("[name=car]").val().trim();
         var driver = $("[name=driver]").val().trim();

        if (code === "") {
            alert("SPK tidak boleh kosong!");
            return;
        }

        if (car === "") {
            alert("Mobil tidak boleh kosong!");
            return;
        }

        if (driver === "") {
            alert("Sopir tidak boleh kosong!");
            return;
        }

        var ajaxData = {
            workOrderCode: $("[name=code]").val(),
            hidWorkOrderKey: $("[name=hidWorkOrderKey]").val(),
            car: $("[name=car]").val(),
            hidCarKey: $("[name=hidCarKey]").val(),
            driver: $("[name=driver]").val(),
            trDesc: $(".note").text(),
            hidDriverKey: $("[name=hidDriverKey]").val()
         };


        // var ajaxData = "action=updateWorkOrder&car="+car+"&code="+code+"&driver="+driver;

        $.ajax({
            type: "POST",
            url: "ajax-trucking-service-work-order.php",
            data: {
               action: "updateWorkOrder",
               data: ajaxData
            },
            success: function(res) {

                if(!res) return;
                var data = parseJSON(res);
                var result = data[0];

                if(result.valid) {
                    alert(result.message);
                    $("[name=code], [name=car], [name=driver]").val('');
                    $("[name=hidWorkOrderKey], [name=hidCarKey], [name=hidDriverKey]").val('');
                    $(".jobType, .shipmentNumber, .jobOrder, .consignee, .stuffingDate, .stuffingLocation, .note").text('');
                    $(".driverName, .drivingLicense, .expirationDate").text('').css({
                            "background-color": "",
                            "color": ""
                        });;
                    $(".registrationNumber, .stnkExpiredDate").text('').css({
                            "background-color": "",
                            "color": ""
                        });;
                    $("[name=code]").focus();
                    // location.reload(); 
                } else {
                    alert(result.message);
                }

            
            }
        });
    }

    function getDataWO(code) {
        var detail = $(".detail-info-wo");

        var ajaxData = "action=getData&code="+code;

        $.ajax({
            type: "GET",
            url: "ajax-trucking-service-work-order.php",
            data: ajaxData,
            success: function(res) {
            var data = parseJSON(res); 

            if (!data || data.length === 0) {
                // detail.hide();
                $("[name=hidWorkOrderKey]").val("");
                // $(".customer").text('');
                // $(".JOCode").text('');
                $(".jobType").text('');
                $(".consignee").text('');
                // $(".depot").text('');
                $(".note").text('');
                $(".stuffingDate").text('');
                $(".stuffingLocation").text('');
                $(".shipmentNumber").text('');
                $(".jobOrder").text('');
                $("[name=code]").val('');
                alert("SPK tidak ditemukan.");
                return;
            } else {
                data = data[0];
                jobtype = data.jobtypename+' - '+data.servicename;
                // detail.show();
                // $(".customer").text(data.customername);
                // $(".JOCode").text(data.serviceordercode);
                $(".jobType").text(jobtype);
                $(".consignee").text(data.consigneename);
                // $(".depot").text(data.depotname);
                $(".note").text(data.trdesc);
                $(".stuffingLocation").text(data.locationname);
                $(".shipmentNumber").text(data.shipmentnumber);
                $(".jobOrder").text(data.serviceordercode);
                $("[name=hidWorkOrderKey]").val(data.pkey);
                $(".stuffingDate").text(moment(data.stuffingdatetime).format(_DATE_TIME_FORMAT_));
                $("[name=driver]").focus();
            }
                
            
            }
        });
    }

    function clearDriver(){

                    //$("[name=hidDriverKey], [name=driver]").val("");
                    $(".driverName, .drivingLicense, .expirationDate").text(''); 
                    $(".expirationDate").css({
                        "background-color": "",
                        "color": ""
                    });
    }
    
    function getDataDriver() {
        // var ajaxData = "action=getDataDriver&name="+driver+"&isdriver=1";
        //console.log("in")
        var driverkey = $("[name=hidDriverKey]").val(); 
        //var ajaxData = "action=getDataDriver&driverkey="+driverkey+"&isdriver=1";        
        var ajaxData = "action=getDataRowById&pkey="+driverkey;        
        
        $.ajax({
            type: "GET",
            //url: "ajax-trucking-service-work-order.php",
            url: "../ajax-employee.php",
            data: ajaxData, 
            async: false,
            beforeSend:function (xhr){
               clearDriver();
            },
            success: function(res) {
            var data = parseJSON(res); 

            if (!data || data.length === 0) {
                // detail.hide(); 
                //clearDriver();
                alert("Sopir tidak ditemukan.");
                return;
            } else {
                data = data[0];
                // detail.show();

                var exp = moment(data.drivinglicenseexpdate);
                var today = moment();
                var diffDays = exp.diff(today, 'days');
 
                $(".expirationDate").text(moment(data.drivinglicenseexpdate).format(_DATE_TIME_FORMAT_));
                if (diffDays <= 14) {
                    $(".expirationDate").css({
                        "background-color": "red",
                        "color": "white"
                    });
                } else {
                    $(".expirationDate").css({
                        "background-color": "",
                        "color": ""
                    });
                }
                $(".drivingLicense").text(data.drivinglicense); 
                $(".driverName").text(data.name);
                // $(".expirationDate").text(data.drivinglicenseexpdate);
                //$("[name=hidDriverKey]").val(data.pkey);
                $("[name=car]").focus();
                getDataCar(true);

            }
                
            
            }
        });
    }

    function getDataCar(isDriver) {
        var driverKey = $("[name=hidDriverKey]").val();
        var car = $("[name=car]").val();

        if (isDriver) {
            var ajaxData = "action=getDataCar&driverKey="+driverKey;
        } else {
            var ajaxData = "action=getDataCar&policenumber="+car;
        }

        $.ajax({
            type: "GET",
            url: "ajax-trucking-service-work-order.php",
            data: ajaxData,
            success: function(res) {
            var data = parseJSON(res); 
            if (!data || data.length === 0) {
                if (!isDriver)
                    alert("Mobil tidak ditemukan.");
                    $("[name=car]").val('');
                    $("[name=hidCarKey]").val("");
                    $(".registrationNumber").text('');
                    $(".stnkExpiredDate").text('');
                    $(".stnkExpiredDate").css({
                        "background-color": "",
                        "color": ""
                    });
                return;
            } else {
                data = data[0];

                var exp = moment(data.licenseexpirydate);
                var today = moment();
                var diffDays = exp.diff(today, 'days');

                var car = data.code+ ' / '+data.policenumber;

                $("[name=hidCarKey]").val(data.pkey);
                $(".registrationNumber").text(car);
                $(".stnkExpiredDate").text(moment(data.licenseexpirydate).format(_DATE_TIME_FORMAT_));
                if (diffDays <= 14) {
                    $(".stnkExpiredDate").css({
                        "background-color": "red",
                        "color": "white"
                    });
                } else {
                    $(".stnkExpiredDate").css({
                        "background-color": "",
                        "color": ""
                    });
                }
                $("[name=car]").val(data.code);
                if (isDriver) {
                    $("[name=car]").val(data.code);
                } else {
                    // updateData();
                }
            }

            $("[name=btnSave]").focus();
                
            
            }
        });
    }

	
   jQuery(document).ready(function() {

        $("[name=code]").focus();

        $("[name=code], [name=driver], [name=car]").on("focus", function() {
            $(this).select();
        });

        $("[name=code]").on("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                var code = $(this).val().trim();


                if (code === "") {
                    alert("Kode tidak boleh kosong!");
                    return;
                }
                getDataWO(code);
            }
        });

        // $("[name=driver]").on("keydown", function(e) {
        //     if (e.key === "Enter") {
        //         e.preventDefault();
        //         var driver = $(this).val().trim();


        //         if (driver === "") {
        //             alert("Sopir tidak boleh kosong!");
        //             return;
        //         }
        //         getDataDriver(driver);
        //     }
        // });

        $("[name=car]").on("keydown", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                var car = $(this).val().trim();

                 if (car === "") {
                    alert("Mobil tidak boleh kosong!");
                    return;
                }

                getDataCar(false);
            }
        });

        $( "[name=btnSave]" ).on( "click", function() { 
		 	// disabledButton($(this));   
		    updateData();  
	    });

    });
   

</script>

</head>
   <body> 
    <div class="dashboard">
        <div style="margin:2em">    
		  
	    <h1>UPDATE SPK</h1>
		  
        <div style="width:100%; margin:auto; " class="tab-panel-form">
            <div class="notification-msg"></div>
            <div class="barcode-input">
                <?php echo $obj->inputText('code', array('etc' => 'style="text-align:left" placeholder="'.ucwords($obj->lang['WOCode']).'"')); ?>
                <?php echo $obj->inputHidden('hidWorkOrderKey', array('overwritePost' => true)); ?> 
            </div>  
            <div style="clear:both; height: 1em"></div>
            <div class="div-table detail-info-wo">
                <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['jobOrder']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 jobOrder"></div>
                </div>
                <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['bookingNumber']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 shipmentNumber"></div>
                </div>
                <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['jobType']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 jobType"></div>
                </div>
                 <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['consignee']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 consignee"></div>
                </div>
                
                 <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['stuffingDate']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 stuffingDate"></div>
                </div>
                
                 <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['stuffingLocation']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 stuffingLocation"></div>
                </div>
                
                 <div class="div-table-row" >
                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['note']); ?></div>
                    <div class="div-table-col-3">:</div>
                    <div class="div-table-col-3 note"></div>
                </div>
            </div>
            
            <div style="clear:both;"></div> 
            
            <div class="flex" style="gap:1em">
                <div>
                    <div style="clear:both; height: 2em"></div> 
                    <div class="barcode-input">
                        <?php echo $obj->inputAutoComplete([
                            "revalidateField" => true,
                            "element" => ["value" => "driver", "key" => "hidDriverKey"],
                            "source" => [
                                "url" => "../ajax-employee.php",
                                "data" => ["action" => "searchData", "isdriver" => "1"],
                            ],
                            "placeholder" => $obj->lang["driver"],
                            "callbackFunction" => "getDataDriver()"
                            ]); ?>
                        <?php // echo $obj->inputText('driver', array('etc' => 'style="text-align:left" placeholder="'.ucwords($obj->lang['driver']).'"')); ?>
                        <?php // echo $obj->inputHidden('hidDriverKey', array('overwritePost' => true)); ?> 
                    </div>  

                    <div style="clear:both; height: 1em"></div>
                    <div class="div-table detail-info-wo">
                            <div class="div-table-row" >
                                <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['name']); ?></div>
                                <div class="div-table-col-3">:</div>
                                <div class="div-table-col-3 driverName"></div>
                            </div>
                            <div class="div-table-row" >
                                <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['drivingLicenseExpirationDate']); ?></div>
                                <div class="div-table-col-3">:</div>
                                <div class="div-table-col-3 expirationDate"></div>
                            </div>

                    </div>
                </div>
                <div>
                      <div style="clear:both; height: 2em"></div>
                        <div class="barcode-input">
                            <?php echo $obj->inputText('car', array('etc' => 'style="text-align:left" placeholder="'.ucwords($obj->lang['car']).'"')); ?>
                            <?php echo $obj->inputHidden('hidCarKey', array('overwritePost' => true)); ?> 
                        </div>  

                        <div style="clear:both; height: 1em"></div>
                        <div class="div-table detail-info-wo">
                                <div class="div-table-row" >
                                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></div>
                                    <div class="div-table-col-3">:</div>
                                    <div class="div-table-col-3 registrationNumber"></div>
                                </div>
                               <div class="div-table-row" >
                                    <div class="div-table-col-3 row-header"><?php echo ucwords($obj->lang['stnkExpiredDate']); ?></div>
                                    <div class="div-table-col-3">:</div>
                                    <div class="div-table-col-3 stnkExpiredDate"></div>
                                </div>


                        </div> 
                
                </div> 
            </div>
            <div style="clear:both; height:3em"></div>
            <div style="text-align:center; margin-top:1em; width: 50em;">
               <div> <?php echo $obj->inputButton('btnSave', $obj->lang['save'], array('class' => 'btn btn-primary btn-second-tone','etc'   => 'type="button"')); ?> </div>
            </div>
          
        </div>
	   </div>
	</div> 
   </body> 
</html>
