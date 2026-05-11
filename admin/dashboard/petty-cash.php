<?php

include "../../_config.php";
include "../../_include-v2.php";

includeClass(array("PettyCash.class.php"));

$pettyCash = new PettyCash();
$obj = $pettyCash;

$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$isQuickAdd = isset($_GET) && !empty($_GET["quickadd"]) ? true : false;
$_POST["trStartDate"] = date("d / m / Y");
$_POST["trEndDate"] = date("d / m / Y");

$arrType=array('0' => 'Semua', '1' => 'Normal', '2' => $obj->lang['downpayment']);
?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />      
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath . ADMIN_CSS_VERSION; ?>">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>responsive.css" /> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js"></script>
<!-- <script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui-timepicker-addon.min.js"></script>  -->
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>moment.min.js"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery.formatCurrency-1.4.0.min.js" ></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>main-3.111.min.js"></script>  

<title><?php echo $obj->lang["pettyCash"]; ?></title> 

<style>
    .ui-datepicker { z-index: 99999 !important}
	.detail-col-detail {vertical-align: top}
    .detail-col-header {background-color: #fff; position: sticky; top: 0; z-index: 9999 !important} 
    .freeze-col {position: sticky; z-index: 999}
</style>


<script type="text/javascript">
   const _DATE_TIME_FORMAT_ = 'HH:mm';

  var objAndValueForDriverDetailAutoComplete = [];
  var objAndValueForCarDetailAutoComplete = [];
  var objAndValueForItemDetailAutoComplete = [];
  var objAndValueForCustomerDetailAutoComplete = [];
  var objAndValueForLocationFromDetailAutoComplete = [];
  var objAndValueForLocationDetailAutoComplete = [];
  var objAndValueForServiceDetailAutoComplete = [];
  var objAndValueForCoDriverDetailAutoComplete = [];
  var objAndValueForSupplierDetailAutoComplete = [];

  objAndValue = new Array;
  objAndValue.push({object:'hidSupplierKey[]', value :'pkey'});  
  objAndValueForSupplierDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidDriverKey[]', value :'pkey'});  
  objAndValueForDriverDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidCarKey[]', value :'pkey'});  
  objAndValueForCarDetailAutoComplete = objAndValue; 
  
  objAndValue = new Array;
  objAndValue.push({object:'hidCostKey[]', value :'pkey'});  
  objAndValueForItemDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidServiceKey[]', value :'pkey'});  
  objAndValueForServiceDetailAutoComplete = objAndValue; 
  
  objAndValue = new Array;
  objAndValue.push({object:'hidCustomerKey[]', value :'pkey'});  
  objAndValueForCustomerDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidStuffingLocationFromKey[]', value :'pkey'});  
  objAndValueForLocationFromDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidStuffingLocationKey[]', value :'pkey'});  
  objAndValueForLocationDetailAutoComplete = objAndValue; 

  objAndValue = new Array;
  objAndValue.push({object:'hidCoDriverKey[]', value :'pkey'});  
  objAndValueForCoDriverDetailAutoComplete = objAndValue; 

  var workOrderDetailRow = 'transaction-detail-row';

	 function rebindEl(){
         bindAutoCompleteForTransactionDetail('driverName[]',objAndValueForDriverDetailAutoComplete,'../ajax-employee.php?action=searchData&isdriver=1&limit=25');  
         bindAutoCompleteForTransactionDetail('coDriverName[]',objAndValueForCoDriverDetailAutoComplete,'../ajax-employee.php?action=searchData&isdriver=1&limit=25');  
         bindAutoCompleteForTransactionDetail('carName[]',objAndValueForCarDetailAutoComplete,'../ajax-car.php?action=searchData&searchField=policenumber&limit=25');   
         bindAutoCompleteForTransactionDetail('costName[]',objAndValueForItemDetailAutoComplete,'../ajax-item.php?action=searchData&itemtype=2&serviceCost=1');  
         // bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForServiceDetailAutoComplete,'../ajax-item.php?action=searchData&itemtype=2&serviceCost=1');  
         bindAutoCompleteForTransactionDetail('customerName[]',objAndValueForCustomerDetailAutoComplete,'../ajax-customer.php?action=searchData&searchField=alias&limit=25');   
         bindAutoCompleteForTransactionDetail('stuffingLocationFromName[]',objAndValueForLocationFromDetailAutoComplete,'../ajax-location.php?action=searchData&limit=25');   
         bindAutoCompleteForTransactionDetail('supplierName[]',objAndValueForSupplierDetailAutoComplete,'../ajax-supplier.php?action=searchData&limit=25');   
         bindAutoCompleteForTransactionDetail('stuffingLocationName[]',objAndValueForLocationDetailAutoComplete,'../ajax-location.php?action=searchData&limit=25');   
         bindAutoCompleteForTransactionDetail('serviceName[]',objAndValueForServiceDetailAutoComplete,'../ajax-item.php?action=searchData&itemtype=2&serviceCost=0');  
        
        
         $( "[name=btnDeleteRows]" ).on( "click", function() { 
            //  disabledButton($(this));   
             removeDetailRows(this); 
             calculateBalance();
         });

         bindEl($(".add-row-button"),'click', function() {
             addRowData($(this));
         });

         $(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
         $(".input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),0); });
         $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),2); });
         $(".inputautodecimal").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),-2); });

         $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this)); });

         bindEl($("[name='debit[]'], [name='credit[]'], [name='settlementAmount[]']"),'change', function() { calculateBalance(); });  
         bindEl($("[name='chkIsOutsource[]']"),'change', function() { updateIsOutsource(this); });  
         bindEl($("[name='chkIsDownpayment[]']"),'change', function() { 
            calculateBalance();
            updateIsDownpayment(this); 
         });  


        
      $("[name='qtyMulti[]']").unbind("input").on("input", function(e) {
         e.preventDefault();
         // Remove anything that's not a digit
        this.value = this.value.replace(/\D/g, '');

          // Limit to 2 digits
          if (this.value.length > 2) {
            this.value = this.value.slice(0, 2);
          }
     });


      // $( "[name=btnAddDetailRow]" ).on( "click", function() { 
		//  	//  disabledButton($(this));
      //       // addNewTemplateRow("transaction-detail-row",null,null,this.rebindEl);  
      //       // newRow = $(".row-template").clone().removeClass("row-template").addClass(workOrderDetailRow);  
		//      addRowData();  
	   // });
 
	}

      function updateIsOutsource(row) { 
         if (!(row instanceof jQuery))   row = $(row); 
         
         var serviceRow = row.closest(".transaction-detail-row");  
         var isOutsource =  serviceRow.find("[name='chkIsOutsource[]']").val();

         if (isOutsource == 1){ 
            serviceRow.find(".inhouse").hide();
            serviceRow.find(".outsource").show();
         }else{ 
            serviceRow.find(".inhouse").show();
            serviceRow.find(".outsource").hide();
         
            // update DP
            serviceRow.find("[name='dummychkIsDownpayment[]'],[name='chkIsDownpayment[]'], [name='settlementAmount[]']").val(0);
            serviceRow.find("[name='dummychkIsDownpayment[]']").prop("checked",false);
            serviceRow.find("[name='settlementAmount[]']").hide();
            calculateBalance();
         }
         
      }

      function updateIsDownpayment(row) {
         var serviceRow = $(row).closest(".transaction-detail-row");  
         var isDP =  serviceRow.find("[name='chkIsDownpayment[]']").val();

         if (isDP == 1){ 
            serviceRow.find(".is-downpayment").show();
         }else{ 
            serviceRow.find(".is-downpayment").hide();
            serviceRow.find("[name='settlementAmount[]']").val(0).change(); 
         }
      }

      function calculateBalance() { 
         var startingBalance = parseFloat(unformatCurrency($("[name=startingBalance]").val())) || 0;
         var endingBalance = parseFloat(unformatCurrency($("[name=endingBalance]").val())) || 0;
         // var qty =  parseFloat(unformatCurrency(serviceRow.find("[name='qtyDetail[]']").val())) || 0;
         
         $("[name='debit[]']").each(function() {   
            detailRow = $(this).closest(".transaction-detail-row"); 
            var debit = parseFloat(unformatCurrency($(this).val())) || 0;
            var credit = parseFloat(unformatCurrency(detailRow.find("[name='credit[]']").val())) || 0;

            var settlement = parseFloat(unformatCurrency(detailRow.find("[name='settlementAmount[]']").val())) || 0;            // var balance = debit - credit;
            startingBalance = startingBalance + debit - credit - settlement;
            detailRow.find("[name='balance[]']").val(startingBalance).blur();
         }) 
         $("[name=endingBalance]").val(startingBalance).blur();
      }

      function addRowData(row) { 
         var currentRow = $(row).closest(".transaction-detail-row");  
         newRow = $(".row-template").clone().removeClass("row-template").addClass(workOrderDetailRow); 
         newRow.find(".input-date").removeClass("hasDatepicker");
         newRow.find(".input-date").removeAttr("id"); 
         newRow.find(".input-date").datepicker({  showButtonPanel: true, currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true, defaultDate: new Date(), maxDate : '+0D' }).datepicker("setDate", new Date());
         newRow.insertAfter(currentRow);
         
         if (row == undefined) {
            $(".transaction-detail").append(newRow); 
         } else {
            newRow.insertAfter(currentRow);
         }
         updateIsOutsource(newRow);
         updateIsDownpayment(newRow);
         updateRowNumber($(".transaction-detail")); 
     
         rebindEl();
      }

      function importData() {
         
         var trStartDate = convertDateToStandartFormat($("[name=trStartDate]").val());
         var trEndDate = convertDateToStandartFormat($("[name=trEndDate]").val());
         var coaKey = $("[name=hidCOAKey]").val();
         var supplierKey = $("[name=hidSupplierKey]").val();
         var typeKey = $("[name=selType]").val();
         let arrKey = [];

         var ajaxData = "action=getData&startdate="+trStartDate+"&enddate="+trEndDate+"&coakey="+coaKey+"&supplierkey="+supplierKey+"&typekey="+typeKey;

         updateCOAValue() ;

         $.ajax({
            type: "GET",
            async: true,
            url: "ajax-petty-cash.php",
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
                  $newRow.find(".input-date").removeClass("hasDatepicker");
                  $newRow.find(".input-date").removeAttr("id"); 
                  $newRow.find(".input-date").datepicker({  showButtonPanel: true, currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true, maxDate : '+0D'  }); 
                  rebindEl();
                  arrKey.push(data[i].pkey);
 
                  updateRowValue($newRow,data[i]);  
               }

               $("[name=data]").val(JSON.stringify(arrKey));

               if (!data || data.length == 0) { 
                   addRowData();
               }
               
               
               updateRowNumber($(".transaction-detail"));  
		 	   disabledButton($("[name=btnImport]"),false);   
            }
         });
      }

      function setQtyMulti(row, value) {
         // pastikan value jadi angka
         let num = parseFloat(value) || 0;
         
         // format ke ribuan + 2 desimal format Indonesia
         let formatted = num.toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
         });
         
         // isi ke input + trigger blur
         row.find("[name='qtyMulti[]']").val(formatted).blur();
      }

	   
      function updateCOAValue(){ 

         var trStartDate = $("[name=trStartDate]").val();
         var trEndDate = $("[name=trEndDate]").val();
         var coaKey = $("[name=hidCOAKey]").val();

         var ajaxData = "action=getCOAmout&startdate="+trStartDate+"&enddate="+trEndDate+"&coakey="+coaKey;
         $.ajax({
            type: "GET",
            async: true,
            url: "ajax-petty-cash.php",
            data: ajaxData, 
              beforeSend:function (xhr){    
                    $(".gps-location").hide();
	            }, 
            success: function(res) {
               if(!res) return;
               var data = parseJSON(res);  
               // var data = res;  

               let num = parseFloat(data.balance) || 0;
         
               // format ke ribuan + 2 desimal format Indonesia
               let formatted = num.toLocaleString('id-ID', {
                  minimumFractionDigits: 2,
                  maximumFractionDigits: 2
               });

               $("[name=startingBalance]").val(data.balance).blur();
               $("[name=endingBalance]").val(data.balance).blur();
               // $("[name=endingBalance]").val(formatted).blur();
               
            }
         });
      }
	   
	  function updateRowValue(row,data){
         var startingBalance = $("[name=startingBalance]").val();
         var endingBalance = $("[name=endingBalance]").val();
		    
		   //  var policenumber = (data.carkey !== null) ? data.policecode + ' - ' + data.policenumber : '';
		   row.find("[name='trdate[]']").val(data.trdate);
		   row.find("[name='doNumber[]']").val(data.donumber);
		   row.find("[name='costName[]']").val(data.costname);
		   row.find("[name='hidCostKey[]']").val(data.costkey);
		   row.find("[name='stuffingLocationFromName[]']").val(data.stuffinglocationfromname);
		   row.find("[name='hidStuffingLocationFromKey[]']").val(data.stuffinglocationfromkey);
		   row.find("[name='stuffingLocationName[]']").val(data.stuffinglocationname);
		   row.find("[name='hidStuffingLocationKey[]']").val(data.stuffinglocationkey);
			row.find("[name='serviceName[]']").val(data.servicename);
			row.find("[name='hidServiceKey[]']").val(data.servicekey);
			row.find("[name='carName[]']").val(data.policenumber);
			row.find("[name='hidCarKey[]']").val(data.carkey);
			row.find("[name='driverNameDesc[]']").val(data.drivernamedesc);
			row.find("[name='driverName[]']").val(data.drivername);
			row.find("[name='hidDriverKey[]']").val(data.driverkey);
			row.find("[name='coDriverNameDesc[]']").val(data.codrivernamedesc);
			row.find("[name='coDriverName[]']").val(data.codrivername);
			row.find("[name='hidCoDriverKey[]']").val(data.codriverkey);
			row.find("[name='debit[]']").val(parseFloat(data.debit)).blur().change();
			row.find("[name='credit[]']").val(parseFloat(data.credit)).blur().change();
			row.find("[name='qtyMulti[]']").val(parseFloat(data.qtymulti)).blur();
			row.find("[name='hidId[]']").val(data.pkey);
			row.find("[name='trDesc[]']").val(data.trdesc);
			row.find("[name='hidCustomerKey[]']").val(data.customerkey);
			row.find("[name='customerName[]']").val(decodeHTMLEntities(data.customeralias));
			row.find("[name='hidSupplierKey[]']").val(data.supplierkey);
			row.find("[name='supplierName[]']").val(data.suppliername);
			row.find("[name='carOutsource[]']").val(data.caroutsource);
			row.find("[name='chkIsSPK[]']").val(data.isspk).change();
			row.find("[name='chkIsOutsource[]']").val(data.isoutsource).change();
         row.find("[name='chkIsDownpayment[]']").val(data.isdownpayment).change();
         row.find("[name='settlementAmount[]']").val(parseFloat(data.settlementamount)).blur().change();

         //Kalau dari cash bank / refkey tidak kosnsh debit blok dan btn delete di hilangkan 
         if (data.refkey && data.refkey!=0)  {
            row.find("input, textarea").attr('readonly', true);
            row.find("[name='btnDeleteRows']").remove();
            row.find("[name='trdate[]']").removeClass("input-date").addClass("force-readonly").datepicker('destroy');
            
         }
            
         row.find(".inputnumber, .input-integer").blur();
			// row.find("[name='trDate[]']").val(moment(data.trdate).format(_DATE_TIME_FORMAT_));
         // row.find(".inputnumber, .input-integer").blur();

	  }
 

      function parseJSON(data){ 

         data = $.trim(data);

         if(!data) data = '[]'; 
         if(data.length == 0) data = '[]'; 

         return JSON.parse(data);
      }

   function inputNumberOnBlur1(obj,decimal){  	
	  if(obj.val() == "" || !$.isNumeric(unformatCurrency(obj.val())) )  
		 obj.val(0);  
   
	  obj.formatCurrency({roundToDecimalPlace: decimal });
   }

	
   jQuery(document).ready(function(){  

      $.ajax({
         url: '../ajax-coa.php',
         async: true,
         type: 'GET',
         data: "action=searchData&iscashbank=1",
         success: function (data) {
            if(!data) return;
            
            var data = parseJSON(data);  
            $("[name=hidCOAKey]").val(data[0].pkey);
            $("[name=COAName]").val(data[0].value);
            importData();
         }
      });

	   $( "[name=btnImport]" ).on( "click", function() { 
		 	 disabledButton($(this));   
		     importData();  
	   });
      

      $(".input-integer").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),0); });
      $(".inputdecimal").each(function() {  if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),2); });
      $(".inputautodecimal").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this),-2); });
      
      $(".inputnumber").each(function() { if($(this).val() == "") $(this).val(0); }).bind( "blur", function(event) { inputNumberOnBlur1($(this)); });
      $(".inputnumber, .inputdecimal, .input-integer, .inputautodecimal").bind("focus",function(event) { inputNumberOnFocus($(this)); } )
	   
	   $( "[name=btnImport]" ).click();
 
      $('#petty-cash').submit(function (e) {
        e.preventDefault();
         var coaKey = $("[name=hidCOAKey]").val();
         var data = $("[name=data]").val();
         var trStartDate = convertDateToStandartFormat($("[name=trStartDate]").val());
         var trEndDate = convertDateToStandartFormat($("[name=trEndDate]").val());
         var btnSave = $('[name=btnSave]');
         
        var formData = $('#petty-cash :input')
        .not('.row-template :input')
        .serialize() + '&action=save' + '&coakey=' + coaKey + '&startdate='+trStartDate+'&enddate='+trEndDate+'&data='+data;
 
       disabledButton(btnSave);  
         
        $.ajax({
            url: 'ajax-petty-cash.php',
            type: 'POST',
            data: formData,
              success: function (res) {
               res = parseJSON(res);
               if (res?.length) {
                  let errors = res.flat().filter(r => !r.valid).map(r => r.message);
                  if (errors.length) alert(errors.join("\n"));
               }else{
                  alert('Data berhasil disimpan');
                
                importData(); // reload ulang data
                let now = new Date();
                document.getElementById("lastUpdate").innerText = now.toLocaleString();
               }
            },
           complete: function (xhr, status) {
              disabledButton(btnSave,false);   
           }
        })
      });

  
   });
   

</script>

</head>
   <body> 
   <div class="dashboard">
      <div style="margin:2em">    
		  
	 <h1>PETTY CASH</h1>
		  
   <div style="width:100%; margin:auto; " class="tab-panel-form">
      <div class="notification-msg"></div>
      <form id="petty-cash"> 
  
      <div style="display:flex; align-items:center; flex-wrap:wrap; gap:0.5em;">
        <div style="font-size:1.5em; padding-right:1em">
            <?php echo ucwords($obj->lang["period"]); ?>
        </div>
        <div style="width:8em">
            <?php echo $obj->inputDate("trStartDate", ["etc" => 'style="text-align:center"']); ?>
            <?php echo $obj->inputHidden("data", ["overwritePost" => true, "disabled" => ""]); ?>
         </div>
        <div>-</div>
        <div  style="width:8em"><?php echo $obj->inputDate("trEndDate", ["etc" => 'style="text-align:center"']); ?></div>
        <div style="width:13em">
            <?php echo $obj->inputAutoComplete([
               "revalidateField" => true,
               "element" => ["value" => "COAName", "key" => "hidCOAKey"],
               "source" => [
                  "url" => "../ajax-coa.php",
                  "data" => ["action" => "searchData", "iscashbank" => "1"],
               ],
               "placeholder" => $obj->lang["pettyCash"],
            ]); ?>
        </div>
        <div style="width:13em">
            <?php echo $obj->inputAutoComplete([
               "revalidateField" => true,
               "element" => ["value" => "supplierName", "key" => "hidSupplierKey"],
               "source" => [
                  "url" => "../ajax-supplier.php",
                  "data" => ["action" => "searchData"],
               ],
               "placeholder" => $obj->lang["supplier"],
            ]); ?>
        </div>
        <div  style="width:8em"><?php echo  $obj->inputSelect('selType', $arrType); ?></div>
        <div><?php echo $obj->inputButton("btnImport", $obj->lang["showAll"], [
           "class" => "btn btn-primary btn-second-tone",
        ]); ?></div>
        <div><?php echo $obj->inputSubmit("btnSave", $obj->lang["save"], [
           "class" => "btn btn-primary btn-second-tone",
        ]); ?></div>
      </div>
      <!-- Baris kedua -->
      <div style="display:flex; gap:0.5em; margin-top:0.5em;">
            <div style="font-size:1.5em; padding-right:1em">
               <?php echo ucwords($obj->lang["balance"]); ?>
         </div>
         <div><?php echo $obj->inputNumber("startingBalance", [
            "etc" => 'style="text-align:right"',
            "readonly" => true,
         ]); ?></div>
         <div style="padding-top:0.6em">-</div>
         <div><?php echo $obj->inputNumber("endingBalance", [
            "etc" => 'style="text-align:right"',
            "readonly" => true,
         ]); ?></div>
      </div>
      <!-- Tambahin Last Update -->
      <div style="margin-top:0.5em; font-size:1em; color:gray;">
         <?php echo ucwords($obj->lang["lastUpdate"]); ?> : <span id="lastUpdate">00/00/0000 00:00:00</span>
      </div>

      <div style="clear:both; height: 1em"></div> 
          <div class="div-table transaction-detail" style="border-bottom:1px solid #333; width: 116em "> 
         <div class="div-table-row  odd-style-adjustment odd-white; "> 
            <div class="div-table-col detail-col-header freeze-col" style="left: 0em; width: 4em; text-align:right"><?php echo $obj->lang['number']; ?></div>
            <div class="div-table-col detail-col-header freeze-col" style="left: 3.9em;  width: 8.5em;"><?php echo ucwords(
               $obj->lang["date"]
            ); ?></div>
            <div class="div-table-col detail-col-header freeze-col" style="width:8em;  left: 12.2em"><?php echo ucwords(
               $obj->lang["customer"]
            ); ?></div> 
            <div class="div-table-col detail-col-header freeze-col" style="width:15em; left: 20em"><?php echo ucwords(
               $obj->lang["si"]
            ) . " / DO"; ?></div>
            <div class="div-table-col detail-col-header" style="width:17em;"><?php echo ucwords(
               $obj->lang["costName"] . " / " . ucwords($obj->lang["note"])
            ); ?></div>
            <div class="div-table-col detail-col-header" style="width:7em;"><?php echo ucwords(
               $obj->lang["location"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:7em;"><?php echo ucwords(
               $obj->lang["service"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:2em;">OSR</div> 
            <div class="div-table-col detail-col-header" style="width:7em;"><?php echo ucwords($obj->lang["driver"]) .
               " / " .
               ucwords($obj->lang["vendor"]); ?></div>
            <div class="div-table-col detail-col-header" style="width:7.2em;"><?php echo ucwords(
               $obj->lang["car"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:3.5em;text-align:right"><?php echo "Multi"; ?></div> 
            <div class="div-table-col detail-col-header" style="width:2em;text-align:center;">DP</div> 
            <div class="div-table-col detail-col-header" style="width:8em;text-align:right;"><?php echo ucwords(
               $obj->lang["debit"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:8em;text-align:right;" ><?php echo ucwords(
               $obj->lang["credit"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:8em;text-align:right;"><?php echo ucwords(
               $obj->lang["balance"]
            ); ?></div> 
            <div class="div-table-col detail-col-header" style="width:2em;"><?php echo ucwords(
               $obj->lang["workOrder"]
            ); ?></div> 
            <!-- <div class="div-table-col detail-col-header"  ><?php echo ucwords($obj->lang["note"]); ?></div> -->
            <div class="div-table-col detail-col-header" style="width:4em;"></div>
         </div>
         <div class="div-table-row row-template transaction-row   odd-style-adjustment odd-white">
            <div class="div-table-col detail-col-detail row-number freeze-col" style="left: 0em; text-align:right; padding-right:0.5em"></div>
            <div class="div-table-col detail-col-detail freeze-col" style="left: 3.9em">
               <?php echo $obj->inputDate("trdate[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'style="text-align:center"',
               ]); ?>
               <?php echo $obj->inputHidden("hidId[]", ["overwritePost" => true, "disabled" => ""]); ?>
            </div>
            <div class="div-table-col detail-col-detail freeze-col"  style="left: 12.2em">
               <?php echo $obj->inputText("customerName[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'placeholder="' . $obj->lang["pleasestarttyping"] . '"',
               ]); ?>
               <?php echo $obj->inputHidden("hidCustomerKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
            </div> 
            <div class="div-table-col detail-col-detail freeze-col"  style=" left: 20em">
               <?php echo $obj->inputText("doNumber[]", ["overwritePost" => true, "class" => "form-control"]); ?>
            </div>
            <div class="div-table-col detail-col-detail">
				<?php echo $obj->inputText("costName[]", ["overwritePost" => true, "class" => "form-control"]); ?>
                <?php echo $obj->inputHidden("hidCostKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
                <?php echo $obj->inputTextArea("trDesc[]", [
                   "overwritePost" => true,
                   "class" => "form-control",
                   "etc" => 'style="height:2.5em; margin-top:0.2em"',
                ]); ?>
            </div>
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText("stuffingLocationFromName[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'placeholder="Pick up point"',
               ]); ?>
               <?php echo $obj->inputHidden("hidStuffingLocationFromKey[]", [
                  "overwritePost" => true,
                  "disabled" => "",
               ]); ?>
               <?php echo $obj->inputText("stuffingLocationName[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'placeholder="Area terjauh" style=" margin-top:0.2em"',
               ]); ?>
               <?php echo $obj->inputHidden("hidStuffingLocationKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText("serviceName[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'placeholder="Layanan"',
               ]); ?>
               <?php echo $obj->inputHidden("hidServiceKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" style="text-align:center">
               <?php echo $obj->inputCheckBox("chkIsOutsource[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" style="text-align:center">
               <?php echo $obj->inputText("supplierName[]", [
                  "overwritePost" => true,
                  "class" => "form-control outsource",
                  "etc" => 'placeholder="Vendor"',
               ]); ?>
               <?php echo $obj->inputHidden("hidSupplierKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
               <?php echo $obj->inputText("driverNameDesc[]", [
                  "overwritePost" => true,
                  "class" => "form-control inhouse",
                  "etc" => 'placeholder="Sopir"',
               ]); ?>
               <!-- <?php echo $obj->inputText("driverName[]", ["overwritePost" => true, "class" => "form-control"]); ?>
               <?php echo $obj->inputHidden("hidDriverKey[]", ["overwritePost" => true, "disabled" => ""]); ?> -->
            
               <?php echo $obj->inputText("coDriverNameDesc[]", [
                  "overwritePost" => true,
                  "class" => "form-control inhouse",
                  "etc" => 'placeholder="Asisten Sopir"  style=" margin-top:0.2em"',
               ]); ?>
               <!-- <?php echo $obj->inputText("coDriverName[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'style="margin-top:0.2em"',
               ]); ?>
               <?php echo $obj->inputHidden("hidCoDriverKey[]", ["overwritePost" => true, "disabled" => ""]); ?> -->
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputText("carName[]", [
                  "overwritePost" => true,
                  "class" => "form-control inhouse",
                  "etc" => 'placeholder="No Polisi"',
               ]); ?>
               <?php echo $obj->inputHidden("hidCarKey[]", ["overwritePost" => true, "disabled" => ""]); ?>
               <?php echo $obj->inputText("carOutsource[]", [
                  "overwritePost" => true,
                  "class" => "form-control outsource",
                  "etc" => 'placeholder="No Polisi Vendor"',
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputNumber("qtyMulti[]", [
                  "overwritePost" => true,
                  "etc" => 'style="text-align:right;" ',
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" style="text-align:center">
               <?php echo $obj->inputCheckBox("chkIsDownpayment[]", ["overwritePost" => true, "class" => "form-control outsource"]); ?> 
            </div> 
            <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputNumber("debit[]", [
                  "overwritePost" => true,
                  "etc" => 'style="text-align:right;" ',
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" >
               <?php echo $obj->inputNumber("credit[]", [
                  "overwritePost" => true,
                  "etc" => 'style="text-align:right;" ',
               ]); ?><?php echo $obj->inputNumber("settlementAmount[]", [
                  "overwritePost" => true,
                  "class" => "form-control inputnumber is-downpayment",
                  "etc" => 'style="text-align:right;margin-top:.1em;" ',
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" >
               <?php echo $obj->inputNumber("balance[]", [
                  "readonly" => true,
                  "overwritePost" => true,
                  "etc" => 'style="text-align:right;" ',
               ]); ?>
            </div> 
            <div class="div-table-col detail-col-detail" style="text-align:center">
               <?php echo $obj->inputCheckBox("chkIsSPK[]", ["overwritePost" => true, "class" => "form-control"]); ?>
            </div> 
            <!-- <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputTextArea("trDesc[]", [
                  "overwritePost" => true,
                  "class" => "form-control",
                  "etc" => 'style="height:5em;"',
               ]); ?>
            </div> -->
            
            <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>">
               <div class="flex">
                <div><?php echo $obj->inputLinkButton("btnAddDetailRow", '<i class="fas fa-plus-circle"></i>', [
                   "class" => "btn btn-link add-row-button",
                ]); ?></div>
                <div><?php echo $obj->inputLinkButton("btnDeleteRows", '<i class="fas fa-times"></i>', [
                   "class" => "btn btn-link remove-button",
                   "etc" => 'tabIndex="-1" style="padding:6px 0;"',
                ]); ?></div>
               </div>
            </div> 
 
            <!-- <div class="div-table-col detail-col-detail">
               <?php echo $obj->inputButton("btnSave", $obj->lang["save"], [
                  "class" => "btn btn-primary btn-second-tone",
                  "etc" => 'style="min-width:0"',
               ]); ?>
            </div> -->
         </div> 
         </div>
      </form>

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
