<?php
require_once '../_config.php'; 
require_once '../_include.php'; 
$obj = $preorderItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));


$formAction = 'preorderItemList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$_POST['txtClosingDate'] = date('d / m / Y');

if (!empty($_GET['id'])){
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];
	$_POST['txtClosingDate'] = $obj->formatDBDate($rs[0]['closingdate'],'d / m / Y');
	$_POST['poPrice'] = $obj->formatNumber($rs[0]['poprice'],0);
	$_POST['slot'] = $obj->formatNumber($rs[0]['slot'],0);
	$_POST['eta'] = $rs[0]['eta'];
	$_POST['trDesc'] = $rs[0]['trdesc'];
	
	$rsItem = $item->getDataRowById($rs[0]['itemkey']);
	$_POST['hidItemKey'] = $rsItem[0]['pkey'];
	$_POST['itemName'] = $rsItem[0]['name'] ;
	
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
                            message: phpErrorMsg.code[1]
                        }, 
                    }
				},
				
				slot: {
					validators: { 
				 		greaterThan: {
							value: 0,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.slot[3]
						}, 
					}
				},
				poPrice: {
					validators: { 
				 		greaterThan: {
							value: 0,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.sellingPrice[3]
						}, 
					}
				},
				eta: {
					validators: {
                        notEmpty: {
                            message: phpErrorMsg.eta[1]
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
		
		 $("#" + selectedTab.newPanel[0].id + " .inputnumber")
		 .each(function() {  
		    if($(this).val() == "") $(this).val(0); 
		 })
		 .bind( "blur", function(event) { 
		   inputNumberOnBlur($(this));
		});
		 
		$( "#" + selectedTab.newPanel[0].id + " [name=itemName]" ).autocomplete({
		  source: "ajax-item.php?action=searchData",
		  minLength: 1,
		  select: function( event, ui ) {      
		   		$("#defaultForm-"+selectedTab.newPanel[0].id + " [name=hidItemKey]" ).val(ui.item.pkey); 
			},   
		  change: function( event, ui ) { 
		  		 if (ui.item == null) 
					clearAutoCompleteInput(this,'hidItemKey');
				 
			},
		}).change(function() {
		   if ($(this).val() == "") 
					clearAutoCompleteInput(this,'hidItemKey'); 
		});
		

		
	});
		
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
   	<?php echo $obj->input('hidden','hidId'); ?>
    <?php echo $obj->input('hidden','action'); ?>
	<?php echo $obj->input('hidden','hidItemKey'); ?>
		
		<div class="div-table" style="width:100%; ">
			<div class="div-table-row">
				<div class="div-table-col"  style="width:49%; text-align:center">
					<div class="div-table-tab-form" style="margin:auto;">
						<div class="div-table-caption border-orange">Informasi Preorder</div>
						<div class="div-table-row form-group">
							<div class="div-table-col-5 div-table-col-header">
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
							 $code =  $obj->input('text','code');   ?>
					
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
								<label class="col-lg-1 control-label">Tanggal Berakhir</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','txtClosingDate',true,'','readonly="readonly" style="float:left;"', 'form-control input-date'); ?>
								</div> 
							</div> 
						 </div>
						 <div class="div-table-row form-group">
							<div class="div-table-col-5" >
								<label class="col-lg-1 control-label">Item</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12">     
									  <?php echo $obj->input('text','itemName'); ?>
								</div> 
							</div> 
						 </div>
						  <div class="div-table-row form-group">
							<div class="div-table-col-5" >
								<label class="col-lg-1 control-label">Harga Preorder</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','poPrice',true,'','','form-control inputnumber'); ?>
								</div>
							</div> 
						 </div>
						 <div class="div-table-row form-group">
							<div class="div-table-col-5" >
								<label class="col-lg-1 control-label">Slot</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','slot',true,'','','form-control inputnumber'); ?>
								</div>
							</div> 
						 </div> 
						  <div class="div-table-row form-group">
							<div class="div-table-col-5" >
								<label class="col-lg-1 control-label">ETA</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','eta'); ?>
								</div>
							</div> 
						 </div> 
					</div>
				</div>
				
				<div class="div-table-col"  style="width:2%; text-align:center"> </div>
				<div class="div-table-col"  style="width:49%; text-align:center">
					 <div class="div-table-tab-form" style="margin:auto;">
							<div class="div-table-caption border-green">Catatan</div>
							<div class="div-table-row form-group"> 
								<div class="div-table-col-5">
									<div class="col-lg-12">  
										  <?php echo  $obj->inputTextArea('trDesc',true,'','style="height:14em;"'); ?>
									</div> 
								</div> 
							</div>   
					  </div>    
				</div>
			</div>
		</div>

 	   <div style="clear:both"></div>
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
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