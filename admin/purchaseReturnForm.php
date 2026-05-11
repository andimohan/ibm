<?php
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $purchaseReturn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));


$formAction = 'purchaseReturnList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];


$editWarehouseInactiveCriteria = '';

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');

if (!empty($_GET['id'])){
	$id = $_GET['id'];
	$rs = $obj->getDataRowById($id);
    
	$rsDetail = $obj->getDetailById($id);

	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey'];
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;
	$_POST['trDesc'] = $rs[0]['trdesc'];

    $_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);

	$_POST['action'] = 'edit';
}else{

	$_POST['action'] = 'add';

	if($useAutoCode == 1)
		$_POST['code'] = 'XXXXXXXX';
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');

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

        supplierName: {
                   validators: {
                       notEmpty: {
                           message: phpErrorMsg.supplier[1]
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

    $( "#" + selectedTab.newPanel[0].id + " [name=supplierName]" ).autocomplete({
      source: "ajax-supplier.php?action=searchData",
      minLength: 1,
      select: function( event, ui ) {
          $("#defaultForm-"+selectedTab.newPanel[0].id + " [name=hidSupplierKey]" ).val(ui.item.pkey);
      },
      change: function( event, ui ) {
           if (ui.item == null)
          clearAutoCompleteInput(this,'hidSupplierKey');

      },
    }).change(function() {
       if ($(this).val() == "")
          clearAutoCompleteInput(this,'hidSupplierKey');
    });

		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});
        objAndValueForDetailAutoComplete[tabID] = objAndValue;


		// DETAIL CLONE
		 $("#defaultForm-"+selectedTab.newPanel[0].id+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("purchase-return-row-template");
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID] ,'ajax-item.php?action=searchData');
        });

 
        <?php if (empty($_GET['id'])){ ?> 
        addNewTemplateRow("purchase-return-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData');


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
    <?php echo $obj->input('hidden','hidSupplierKey'); ?>

        <div class="div-table" style="width:100%; ">
                <div class="div-table-row">
                    <div class="div-table-col"  style="width:49%; text-align:center">
      						  <div class="div-table-tab-form" style="margin:auto;">
              <div class="div-table-caption border-orange">Informasi Umum</div>
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
                    <label class="col-lg-1 control-label">Gudang</label>
                </div>
                <div class="div-table-col-5">
                    <div class="col-lg-12">
                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                    </div>
                </div>
             </div>

             <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Pemasok</label>
                </div>
                <div class="div-table-col-5">
                    <div class="col-lg-12">
                          <?php echo $obj->input('text','supplierName'); ?>
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

        <div style="clear:both; height:2em;"></div>

        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header">Item</div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;">Jumlah</div>
                    <div class="div-table-col detail-col-header"  style="width:70px"></div>
                </div>

				<?php
 
                    for ($i=0;$i<count($rsDetail); $i++){

                        $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

                        $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey'];
                        $_POST['itemName[]'] =  $rsItem[0]['name'];
                        $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                ?>      
                    <div class="div-table-row transaction-detail-row">
                        <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',true,'',''); ?><?php echo $obj->input('hidden','hidItemKey[]'); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','qty[]',true,'','style="text-align:right;"','form-control inputnumber'); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'','btn btn-link remove-button'); ?></div>
                    </div>
                <?php } ?> 

                 <!-- Template for dynamic field -->
                 <div class="div-table-row purchase-return-row-template" style="display:none;"  >
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','itemName[]',false,'','disabled="disabled"'); ?> <?php echo $obj->input('hidden','hidItemKey[]',false,'','disabled="disabled"'); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->input('text','qty[]',false,'','disabled="disabled" style="text-align:right;" ','form-control inputnumber'); ?></div>
					<div class="div-table-col detail-col-detail"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'','btn btn-link remove-button'); ?></div>
                  </div>
 

         </div>

          <div style="clear:both; height:1em;"></div>
          <div style="float:left; display:inline-block;"><?php echo $obj->input('button','btnAddRows',false,$obj->lang['addRows'],'style="margin-top:0.2em;"'); ?></div>

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
