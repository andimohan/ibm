<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $itemPromo;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'itemPromoList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$rsItemPromoDetail = array();

$_POST['txtStartDate'] = date('d / m / Y');
$_POST['txtEndDate'] = date('d / m / Y');

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
    
	$rsItemPromoDetail = $obj->getDetailById($id);
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];
	//$_POST['promoName'] = $rs[0]['promoname'];
	$_POST['txtStartDate'] = $obj->formatDBDate($rs[0]['startdate'],'d / m / Y');
	$_POST['txtEndDate'] = $obj->formatDBDate($rs[0]['enddate'],'d / m / Y');
	$_POST['trNotes'] = $rs[0]['trnotes']; 
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
			 
         $("#defaultForm-"+selectedTab.newPanel[0].id+" .remove-button").click(function() {removeDetailRows(this);});  
		  
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
		 
		 objAndValue = new Array;
		 objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
		 objAndValue.push({object:'priceInUnit[]', value :'sellingprice'});
         objAndValueForDetailAutoComplete[tabID] = objAndValue; 
		 
		 // DETAIL CLONE
		 $("#defaultForm-"+selectedTab.newPanel[0].id+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("promo-item-row-template");
			 bindAutoCompleteForTransactionDetail('itemName[]', objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData');
        });

        <?php if (empty($_GET['id'])){ ?> 
            addNewTemplateRow("promo-item-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData');

        
	});
	
	function promoItemCalculateDetail(obj){    
		var parentObj =  $(obj).parent().parent();
		var priceInUnit =  unformatCurrency(parentObj.find("[name='priceInUnit[]']").val());
		var discount =  unformatCurrency(parentObj.find("[name='discountValueInUnit[]']").val());
		var discountType =  unformatCurrency(parentObj.find("[name='selDiscountType[]']").val());
		 
		if (discount != 0){
			if (discountType == 2)
				discount = discount/100 * priceInUnit;
		}
		
		var promoPrice = (priceInUnit - discount);
		parentObj.find("[name='promoPrice[]']").val(promoPrice).blur(); 
	}
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
   	<?php echo $obj->input('hidden','hidId'); ?>
   	<?php echo $obj->input('hidden','hidModifiedOn'); ?>
    <?php echo $obj->input('hidden','action'); ?>
		
		<div class="div-table" style="width:100%; ">
			<div class="div-table-row">
				<div class="div-table-col"  style="width:49%; text-align:center">
					<div class="div-table-tab-form" style="margin:auto;">
						<div class="div-table-caption border-orange">Informasi Promo</div>
						<div class="div-table-row form-group">
							<div class="div-table-col-5 div-table-col-header">
								<label class="col-lg-1 control-label">Status</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									 <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
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
								<label class="col-lg-1 control-label">Tanggal Mulai</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','txtStartDate',true,'','readonly="readonly" style="float:left;"', 'form-control input-date'); ?>
								</div> 
							</div> 
						 </div>
						 <div class="div-table-row form-group">
							<div class="div-table-col-5" >
								<label class="col-lg-1 control-label">Tanggal Berakhir</label>
							</div> 
							<div class="div-table-col-5">
								<div class="col-lg-12"> 
									  <?php echo $obj->input('text','txtEndDate',true,'','readonly="readonly" style="float:left;"', 'form-control input-date'); ?>
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
										  <?php echo  $obj->inputTextArea('trNotes',true,'','style="height:14em;"'); ?>
									</div> 
								</div> 
							</div>   
					  </div>    
				</div>
			</div>
		</div>
		
		<div style="clear:both; height:2em;"></div>
        
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
			<div class="div-table-row"> 
				<div class="div-table-col detail-col-header">Item</div>
				<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Harga Retail</div>
				<div class="div-table-col detail-col-header" style="width:100px; text-align:right; padding-right:0;"></div>
                <div class="div-table-col detail-col-header" style="width:70px; text-align:right; padding-left:0.2em;">Diskon</div>
				<div class="div-table-col detail-col-header" style="width:100px; text-align:right;">Harga Promo</div>
				<div class="div-table-col detail-col-header" style="width:70px"></div>
			</div>
			
			<?php
               for ($i=0;$i<count($rsItemPromoDetail); $i++){ 
				
					$rsItem = $item->getDataRowById($rsItemPromoDetail[$i]['itemkey']);
				    $decimal = 0;
                    $inputnumber = 'inputnumber';
                        
                    if ($rsItemPromoDetail[$i]['discounttype']  == 2){ 
                            $decimal = 2;
                            $inputnumber = 'inputdecimal';
                    }
                     

                    $_POST['hidItemKey[]'] =  $rsItemPromoDetail[$i]['itemkey']; 
                    $_POST['itemName[]'] =  $rsItem[0]['name']; 
                    $_POST['priceInUnit[]'] =   $obj->formatNumber($rsItemPromoDetail[$i]['priceinunit']);  
                    $_POST['selDiscountType[]'] =  $rsItemPromoDetail[$i]['discounttype'] ; 
                    $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsItemPromoDetail[$i]['discount'],$decimal);
                    $_POST['promoPrice[]'] = $obj->formatNumber($rsItemPromoDetail[$i]['promoprice']); 
  
				 
			?>
			
			<div class="div-table-row transaction-detail-row"> 
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',true,'',''); ?><?php echo $obj->input('hidden','hidItemKey[]'); ?></div> 
				 <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','priceInUnit[]',true,'','style="text-align:right;" readonly="readonly" onChange="promoItemCalculateDetail(this)"','form-control inputnumber'); ?></div>
				<div class="div-table-col detail-col-detail" style="padding-right:0;"><?php echo $obj->input('text','discountValueInUnit[]',true,'','style="text-align:right;"  onChange="promoItemCalculateDetail(this)"','form-control '. $inputnumber); ?></div>
				<div class="div-table-col detail-col-detail" style="padding-left:0.2em;"><?php echo $obj->inputSelect('selDiscountType[]', $obj->arrDiscountType,true,'',' style="width:6.1em;"  onChange="promoItemCalculateDetail(this)"'); ?></div>
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('text','promoPrice[]',true,'','style="text-align:right;" readonly="readonly"','form-control inputnumber'); ?></div>
		        <div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="promoItemCalculateDetail()"','btn btn-link remove-button'); ?></div>
       		</div>
			 
            <?php  }   ?> 
            
			<!-- Template for dynamic field -->
			<div class="div-table-row promo-item-row-template" style="display:none;"  > 
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',false,'','disabled="disabled"'); ?> <?php echo $obj->input('hidden','hidItemKey[]',false,'','disabled="disabled"'); ?></div> 
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('text','priceInUnit[]',false,'','disabled="disabled" style="text-align:right;" readonly="readonly"   onChange="promoItemCalculateDetail(this)"','form-control inputnumber'); ?></div>
				<div class="div-table-col detail-col-detail" style="padding-right:0;"><?php echo $obj->input('text','discountValueInUnit[]',false,'','disabled="disabled" style="text-align:right;"  onChange="promoItemCalculateDetail(this)"','form-control inputnumber'); ?></div>
				<div class="div-table-col detail-col-detail" style="padding-left:0.2em;"><?php echo  $obj->inputSelect('selDiscountType[]', $obj->arrDiscountType,false,0,' disabled="disabled"  style="width:6.1em;"  onChange="promoItemCalculateDetail(this)"'); ?></div>
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('text','promoPrice[]',false,'','disabled="disabled" readonly="readonly" style="text-align:right;"','form-control inputnumber'); ?></div>
				<div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'attrhandler="promoItemCalculateDetail()"','btn btn-link remove-button'); ?></div> 
			  </div>    
	
		</div>
		<div style="clear:both; height:1em;"></div> 
		<div style="float:left; display:inline-block;"><?php echo $obj->input('button','btnAddRows',false,$obj->lang['addRows'],'style="margin-top:0.2em;"'); ?></div>
         
          
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