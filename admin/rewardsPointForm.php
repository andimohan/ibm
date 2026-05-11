<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $rewardsPoint;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));


$formAction = 'rewardsPointList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$_POST['trDate'] = date('d / m / Y');
 
	
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
	$rs = $obj->getDataRowById($id); 
    
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate']);
	$_POST['point'] = $obj->formatNumber($rs[0]['point']); 
	$_POST['hidCustomerKey'] = $rs[0]['customerkey'];
	$rsCust = $customer->getDataRowById($_POST['hidCustomerKey']); 
	$_POST['customerName'] = $rsCust[0]['name']; 
	$_POST['selStatus'] = $rs[0]['statuskey'];
	$_POST['notes'] = $rs[0]['trdesc']; 
    $_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
 
	  
	
	$_POST['action'] = 'edit';
}else{
	$_POST['action'] = 'add';
	
	if($useAutoCode == 1) 
		$_POST['code'] = 'XXXXXXXX';
}  


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript">
 
	
	jQuery(document).ready(function(){  
		$("#" + selectedTab.newPanel[0].id + " #defaultForm").attr("id","defaultForm-"+selectedTab.newPanel[0].id);  
  
		$( "#" + selectedTab.newPanel[0].id + " [name=customerName]" ).autocomplete({
		  source: "ajax-customer.php?action=searchData",
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#defaultForm-"+selectedTab.newPanel[0].id + " [name=hidCustomerKey]" ).val(ui.item.pkey); 
			},  
		  search: function( event, ui ) { },
		  change: function( event, ui ) { 
		  		 if (ui.item == null) 
					clearAutoCompleteInput(this,'hidCustomerKey');
				 
			},
		}).change(function() {
		   if ($(this).val() == "") 
					clearAutoCompleteInput(this,'hidCustomerKey'); 
		});
		
		
		$( "#" + selectedTab.newPanel[0].id + " [name=salesCode]" ).autocomplete({
		  source: "searchSalesOrder.php",
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#defaultForm-"+selectedTab.newPanel[0].id + " [name=hidSalesOrderKey]" ).val(ui.item.pkey); 
			},  
		  search: function( event, ui ) { },
		  change: function( event, ui ) { 
		  		 if (ui.item == null) 
					clearAutoCompleteInput(this,'hidSalesOrderKey');
				 
			},
		}).change(function() {
		   if ($(this).val() == "") 
					clearAutoCompleteInput(this,'hidSalesOrderKey'); 
		});
 
		
		 $("#" + selectedTab.newPanel[0].id + " .inputnumber")
			 .each(function() {  
				if($(this).val() == "") $(this).val(0); 
			 })
			 .bind( "blur", function(event) { 
			   inputNumberOnBlur($(this));
	 	});
		
			 
		 $('#defaultForm-' + selectedTab.newPanel[0].id )
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
                            message: phpErrorMsg.code[1],
                        }, 
                    }
                }, 
				
				customerName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.customer[1],
                        }, 
                    }
                }, 
				
				point: {
					validators: { 
				 		 notEmpty: {
                            message: phpErrorMsg.point[1],
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
    <?php echo $obj->input('hidden','hidCustomerKey'); ?>
    <?php echo $obj->input('hidden','hidSalesOrderKey'); ?>
        
         <div class="div-table-tab-form" style="margin:auto; width:500px;">
         
           <div class="div-table-row form-group">
                <div class="div-table-col-5  div-table-col-header">
                    <label class="col-lg-1 control-label">Status</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo  $obj->inputSelect('selStatus', $arrStatus, true,0,'disabled="disabled"'); ?>
                    </div>
                </div> 
             </div>
             
             
             <?php if($useAutoCode == 1)    
 				$code = $obj->input('text','code',true,'','readonly="readonly"', 'form-control readonly');  
            else  
                $code =  $obj->input('text','code',true );   ?>
        
	
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Kode</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo  $code; ?>
                    </div>
                </div> 
             </div>
             
              <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Tanggal</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                          <?php echo $obj->input('text','trDate',true,'','readonly="readonly"','form-control input-date'); ?>
                    </div> 
                </div> 
             </div>  
             
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Nama Pelanggan</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo $class->input('text','customerName'); ?>
                    </div> 
                </div> 
             </div> 
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Transaksi</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo $class->input('text','salesCode'); ?>
                    </div> 
                </div> 
             </div> 
             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Point</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                          <?php echo $obj->input('text','point',true,'','','form-control inputnumber'); ?>
                    </div> 
                </div> 
             </div>  
			 
			 <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Catatan</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
						 <?php echo  $obj->inputTextArea('notes',true,'','style="height:10em;"'); ?>
                    </div> 
                </div> 
             </div>
			 
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
          ?>
        </div>
    </div>
</div> 
</body>

</html>