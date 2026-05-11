<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Employee.class.php');
$obj = createObjAndAddToCol(new Employee());

// tarik semua bulan yg bisa diedit
// tentukan dulu periode awal, patokan dari terakhir period masih open

$_POST['periodDate'] = date('F Y'); 
$_POST['action'] = 'calculateEmployeeCommission';

$arrSales = $employee->generateComboboxOpt(null,array('criteria' => ' and '.$employee->tableName.'.issales = 1 and ('.$employee->tableName.'.statuskey = 2)'));// jgn pake inactive criteria agar tetep bisa lihat transaksi sales laama

$rand = rand(0,100);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>sol.css"/>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>sol.js"></script>
<script type="text/javascript"> 
  
    jQuery(document).ready(function() {
 
         var tabObj = $('#employeeCommissionParameterForm-<?php echo $rand; ?>');
         var parentPanelId = '<?php echo (isset($_GET['reloadAfterUpdate'])) ? $_GET['reloadAfterUpdate'] : ''; ?>';
        
          tabObj.find('[name="btnSave"]').click( function(){   
             
                    $.ajax({ 
                      url: 'ajax-employee-commission',  
                      method : 'POST',
                      data: tabObj.find("#form-period").serialize(), 
                      async: false,
                      success: function(data){   
                          hideOverlayScreen();   
                          updateData(false,parentPanelId);
                      } 
                    }); 
              
            });
        
          tabObj.find('.input-month').datepicker({
                dateFormat: "MM yy",
                changeMonth: true,  
                changeYear: true,
                showButtonPanel: true,
                onClose: function(dateText, inst) {

                    function isDonePressed(){
                        return ($('#ui-datepicker-div').html().indexOf('ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all ui-state-hover') > -1);
                    }

                    if (isDonePressed()){
                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(year, month, 1)).trigger('change');

                         $('.input-month').focusout()//Added to remove focus from datepicker input box on selecting date
                    }
                },
                beforeShow : function(input, inst) {

                    inst.dpDiv.addClass('month-year-datepicker');

                    if ((datestr = $(this).val()).length > 0) { 
                        var d = new Date(datestr);  
                        year = d.getFullYear();
                        month = d.getMonth();
                        $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                        $(this).datepicker('setDate', new Date(year, month, 1));  
                    }
                }
            });
        
         // kedepannya tambahin onload khusus popup saja
         tabObj.find(".close-panel").click( function(){ hideOverlayScreen();  });
         tabObj.find(".multi-selectbox:not(:disabled)").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  }); 
        
        });
    
</script> 
</head>  

<body> 
 <div id="employeeCommissionParameterForm-<?php echo $rand; ?>" class="form-panel" style="width:600px; height: 300px"> 
    <div class="header-panel div-table">
        <div class="div-table-row">
                <div class="title-panel div-table-col"><?php echo $obj->lang['generateEmployeeCommission']; ?></div>
                <div class="close-panel div-table-col"><i class="fas fa-times"></i></div>
        </div>
    </div>
    <div style="clear:both;height: 3em"></div>    
    <form id="form-period" method="post" class="form-horizontal form-default prevent-form-submit">
    
    <div style="clear:both; height: 2em"></div>    
    <div class="form-panel">
        <div class="form-group">
            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label>
            <div class="col-xs-9">
              <?php echo $obj->inputMonth('periodDate'); ?>
            </div>
        </div>  
        <div class="form-group">
            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label>
            <div class="col-xs-9">
              <?php echo  $obj->inputSelect('selSales[]',$arrSales, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox') ); ?>
            </div>
        </div>  
    </div>    
        <div style="clear:both; height: 2em"></div>
    <div class="action-panel" style="text-align:center">
        <?php echo $obj->inputButton('btnSave', $obj->lang['save']); ?> 
        <?php echo $obj->inputHidden('action'); ?> 
    </div>
    </form> 
</div>
</body>
</html>
