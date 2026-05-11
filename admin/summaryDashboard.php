<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array(
                    'Marketplace.class.php',
                    'WidgetSetting.class.php', 
                  ));

$widgetSetting = new WidgetSetting();
$warehouse = new Warehouse();

$_POST['trStartPeriod'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
$_POST['trEndPeriod'] = date('F Y');
  
$arrGraphPanel = array();  
$arrNotificationPanel = array();
 
$allWidgets = $widgetSetting->getWidgets(); 
 
$arrWidgets = json_encode($widgetSetting->getSelectedWidgets());  
$arrNotificationPanel = json_encode($arrNotificationPanel);
//$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1)'),'pkey','name');  

$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and ('.$warehouse->tableName.'.statuskey = 1)'),'-----');


?> 

<!--<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jsapi.js"></script> -->
<script src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">  
	jQuery(document).ready(function(){ 
        var arrWidgets = <?php echo $arrWidgets; ?>; 
        var arrNotificationPanel = <?php echo $arrNotificationPanel; ?>;   
        var dashboard = new Dashboard(arrWidgets, arrNotificationPanel); 
        dashboard.loadOnReady();
    });
    
    function AIAnalyze(obj){
 
         $.ajax({
                        type: "POST",
                        url:  'ajax-ai.php',
                        data:{action:"salesOrderSummary", startPeriod: $("#dashboard [name=trStartPeriod]").val(),endPeriod :  $("#dashboard [name=trEndPeriod]").val(),selWarehouse :  $("#dashboard [name=selWarehouse]").val()},
                    }).done(function( data ) { 
                        //console.log(data);
                        data = parseJSON(data);
                       // Create form dynamically
                        var form = $('<form>', {
                            action: 'https://wintera.co.id/ai-analyze',
                            method: 'POST',
                            target: '_blank'
                        });

                        // Add hidden input
                        $('<input>').attr({ type: 'hidden', name: 'fileData', value: data.fileData }).appendTo(form);

                        // Append form to body, submit, then remove
                        form.appendTo('body').submit().remove();

                    });   
    }
</script>
<div class="panel-data-list">    
 <div class="container"> 
    <div id="dashboard">
		
	<div class="in-tab-overlay widget-setting user-select-none">
		<div class="btn-close-overlay"><i class="fal fa-times"></i></div>   
		<form name="form-widget-setting" class="form-panel flex"  > 
			<div class="consume"> 
				<?php echo $class->inputHidden('hidPanelKey'); ?>
				<?php echo $class->inputHidden('action',  array('value' => 'updateWidgetProperties')); ?>
				<div class="div-table properties-table"> </div>
			</div> 
			<div class="btn-panel"><?php echo $class->inputButton('btnSaveWidgetProperties',ucwords($class->lang['save'])); ?></div> 
		</form>
	</div>
    <div class="in-tab-overlay dashboard-settings"> 
        <div class="btn-close-overlay"><i class="fal fa-times"></i></div>  
		<form name="form-widget-dashboard-setting"> 
            <div style="clear:both; height: 5em"></div>   
            <div class="dashboard-settings-panel">
            <div style="margin-left: 0.7em; margin-bottom:0.5em;"><?php 
                    echo '<span style="margin-right:0.5em">'.$class->inputCheckBox('chkAllWidget').'</span>' ; 
                    echo $class->lang['selectAll']; 
                 ?>
            </div>    
            <ul id="widget-module" >
                <?php  
                    foreach($allWidgets as $row)
                        echo '<li>'.$class->inputCheckBox('chkWidget-'.$row['pkey']). ' '.$row['title'].'</li>'; 
                ?>
            </ul> 
            <div style="clear:both; height: 2em"></div>   
            <div style="padding:0.5em; text-align:center"><?php echo $class->inputButton('btnSaveDashboardSettings',ucwords($class->lang['save'])); ?></div>
            </div>
		</form>
    </div>  
     <ul id="dashboard-btn-action" class="div-table" style="float:right;  display: inline-block; margin-right:2em"> 
        <li><div class="btn-action btn-dashboard-settings"><i class="fas fa-window-restore"></i></div></li>  
        <li><div class="btn-action refresh-graph"><i class="fas fa-sync"></i></div></li>  
        <li>
            <div class="div-table-row mnv-date-range">
                <div class="div-table-col-5" ><?php echo $class->inputMonth('trStartPeriod', array('etc' => 'style="text-align:center"'));  ?></div>  
                <div class="div-table-col-5" style=" text-align:center; width:3em; line-height:2.5em"> s/d </div> 
                <div class="div-table-col-5" ><?php echo $class->inputMonth('trEndPeriod', array('etc' => 'style="text-align:center"')); ?></div> 
            </div>    
        </li>
		 <li>
			<div class="div-table-row">
                <div class="div-table-col-5" ><?php echo  $class->inputSelect('selWarehouse', $arrWarehouse); ?></div>   
            </div>  
		</li> 
    </ul>   
    <div style="clear:both; height: 1em"></div>
    <div id="widgets-panel"></div>       
    <div style="clear:both; height:1em"></div>
    </div>

 </div>  
</div>