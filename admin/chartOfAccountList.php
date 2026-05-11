<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('ChartOfAccount.class.php');
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

$obj = $chartOfAccount;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$arrColumn = $obj->generateDataListColumn($FILE_NAME);

$addDataFile = 'chartOfAccountForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));

 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
$overwriteContextMenu['changeStatus'] = ''; 

    
function generateQuickView($obj,$id){
	return ''; 
}
  
//$chartOfAccount->temporaryUpdate();
$obj->importUrl = 'import/coa';

$runningMonth = $chartOfAccount->getRunningPeriod(); 
$runningMonth = $class->formatDBDate($runningMonth[0]['runningmonth'],'M Y');
 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status','- '.$obj->lang['chooseStatus'].' -');

// ========================================================================== AJAX SECTION ==========================================================================
if (isset($_POST['generateDataRecords']) && !empty($_POST['generateDataRecords']) ){  
	include ('populateDataCOA.php');  
}

// ========================================================================== ADD DATA SECTION ==========================================================================

if (isset($_POST['action']) && !empty($_POST['action']) ){
	include ('dataProcess.php');
}

// ========================================================================== QUICK VIEW SECTION ==========================================================================
if (isset($_POST['generateQuickView']) && !empty($_POST['generateQuickView']) ){
	echo generateQuickView($obj,$_POST['id']);
	die;
}
 

// ======================================================== NAVBAR  
$arrNavbarButton = array();

$addDataActionClass = 'btn-action';
$idAdd = 'btn-add-new';
$idEdit = 'btn-edit-data';
$hasAddAccess = $security->isAdminLogin($obj->securityObject,11,false); 
if(!$hasAddAccess){
 $addDataActionClass .= ' disabled';
 $idAdd = ''; 
}  

if (!empty($addDataFile)){  
    array_push($arrNavbarButton, '<li id="'.$idAdd.'" class="'.$addDataActionClass.'" style="font-size:26px; line-height: 34px"  title="'.$obj->lang['add'].'"><i class="fas fa-plus"></i></li>');
    array_push($arrNavbarButton, '<li id="'.$idEdit.'" class="btn-action"  title="'.$obj->lang['edit'].'"><i class="fas fa-edit"></i></li>');
}


$actionClass = 'btn-action';
$id = 'btn-delete';
if(!$security->isAdminLogin($obj->securityObject,12,false)){
 $actionClass .= ' disabled';
 $id = '';
}
  
array_push($arrNavbarButton, '<li id="'.$id.'" class="'.$actionClass.'" style="font-size:26px; line-height: 34px" title="'.$obj->lang['delete'].'"><i class="fas fa-times"></i></li>');
array_push($arrNavbarButton, '<li id="btn-refresh" class="btn-action"  title="'.$obj->lang['refresh'].'" style="font-size:20px"><i class="fas fa-sync"></i></li>');
$importBtn = ($hasAddAccess) ? '<li id="btn-import"  class="'.$addDataActionClass.'" title="'.$obj->lang['import'].'" ><a href="'.$obj->importUrl.'" target="_blank"><i class="fas fa-file-import"></i></a></li>' :  '<li id="btn-import"  class="'.$addDataActionClass.'" title="'.$obj->lang['import'].'" ><i class="fas fa-file-import"></i></li>'; 
array_push($arrNavbarButton, $importBtn);  
array_push($arrNavbarButton, '<li class="separator">&nbsp;</li>');
array_push($arrNavbarButton, '<li id="btn-expand-all" class="btn-action" title="'.$obj->lang['showDetail'].'" ><i class="far fa-window-maximize"></i></li>');
          
//array_push($arrNavbarButton, '<li class="separator">&nbsp;</li>');
//array_push($arrNavbarButton, '<li id="btn-lock-period" class="btn-action" title="'.$obj->lang['lockPeriod'].'" ><i class="far fa-lock-alt"></i></li>');

if($obj->loadSetting('currencyRevaluation') == 1)
array_push($arrNavbarButton, '<li id="btn-revaluation" class="btn-action" title="'.$obj->lang['revaluation'].'" ><i class="fas fa-file-invoice-dollar"></i></li>');
                    
array_push($arrNavbarButton, '<li class="navbar-right reverse-panel" style="margin-left:0.5em; display:none;">'.$class->inputButton('btnReverseClosingPeriod',$class->lang['reverseClosingPeriod'], array('class' => 'btn btn-primary btn-red-tone closing-button')).'</li>');
array_push($arrNavbarButton, '<li class="navbar-right" style="margin-left:1em">'.$class->inputButton('btnClosingPeriod',$class->lang['closingPeriod'], array('class' => 'btn btn-primary closing-button')).'</li>');
array_push($arrNavbarButton, '<li class="navbar-right" style="padding-top:0.6em"><span class="running-month">'.strtoupper($runningMonth).'</span></li>');

// ======================================================== NAVBAR  
 

?>

<script>
 	$(document).ready(function() {
        
            var selectedTabId = selectedTab.newPanel[0].id;
            var selectedTabObj = $("#" + selectedTabId);
			 
	 		tabParam[selectedTabId].phpDataListFile = "<?php echo $FILE_NAME; ?>";
	 		tabParam[selectedTabId].addDataFile = "<?php echo $addDataFile; ?>"; 
            tabParam[selectedTabId].quickView = false; 
        
			/*if(tabParam[selectedTabId].selectedCriteriaStatusKey == "")  
                tabParam[selectedTabId].selectedCriteriaStatusKey.push("1");*/
            
			updateDataCOA(false);
		     
                			   
            var navbarMenuItem = <?php  echo json_encode($arrNavbarButton); ?>;  
            var navbarMenu = ''; 
            for(var i=0; i<navbarMenuItem.length; i++){
                navbarMenu += navbarMenuItem[i];
            }
        
            selectedTabObj.find('.action-bar-navbar .navbar-nav').append(navbarMenu);
        
			// assign ID ke div data-list
			// blm tau kepake ap gk selanjutnya.... 
			selectedTabObj.find(".data-list").attr("id","data-list-"+selectedTabId);  
			
			//refresh button 
			selectedTabObj.find("#btn-refresh").attr("id","btn-refresh-"+selectedTabId);   
			$("#btn-refresh-" + selectedTabId).bind( "click", function( event ) {  updateDataCOA(false); });
		  	
			//add button 
			selectedTabObj.find("#btn-add-new").attr("id","btn-add-new-"+selectedTabId); 
			$("#btn-add-new-"+selectedTabId).bind( "click", function( event ) {    
				var title = encodeURI(selectedTab.newTab[0].textContent);
             	 addTab(phpLang.add + " - " + title ,"<?php echo $addDataFile ;?>?title=" + title + "&fileName=<?php echo $FILE_NAME; ?>&selectedPanelId="+selectedTabId); 
			});
  
			//edit button 
			selectedTabObj.find("#btn-edit-data").attr("id","btn-edit-data-"+selectedTabId); 
			$("#btn-edit-data-"+selectedTabId).bind( "click", function( event ) {   
				 openTabForEdit();
            });
        
			//delete button 
			selectedTabObj.find("#btn-delete").attr("id","btn-delete-"+selectedTabId); 
			$("#btn-delete-"+selectedTabId ).bind( "click", function( event ) {  
			 	 deleteData();
			});

        
            //expand-all button 
			selectedTabObj.find("#btn-expand-all").attr("id","btn-expand-all-"+selectedTabId);   
			$("#btn-expand-all-" + selectedTabId).bind( "click", function( event ) { expandAll(); }); 

            //lock period 
//			selectedTabObj.find("#btn-lock-period").attr("id","btn-lock-period-"+selectedTabId);   
//			$("#btn-lock-period-" + selectedTabId).bind( "click", function( event ) { openLockPeriodForm(); }); 

        	selectedTabObj.find("#btn-revaluation").attr("id","btn-revaluation-"+selectedTabId);   
			$("#btn-revaluation-" + selectedTabId).bind( "click", function( event ) { updateRevaluation(); }); 
                
            selectedTabObj.find("[name=btnClosingPeriod]").attr("id","btn-closing-all-"+selectedTabId);   
			$("#btn-closing-all-" + selectedTabId).bind( "click", function( event ) {    
                    $(".closing-button").prop('disabled', true);
                    $(this).find(".loading-icon").show();
                    var reverseButton = $(this).closest(".action-bar").find(".reverse-panel"); 
			        $.ajax({
                            type: "POST",
                            url:  'ajax-coa.php', 
                            data : "action=monthlyClosing",
                            success: function(data){   
                                
                                    data = JSON.parse(data);
                                 
                                    if(data[0]['valid']){
                                        alert("Tutup buku selesai");
                                    }else{
                                        alert(data[0]['message']);
                                    }
                                 
                                    updateDataCOA(false);
                                    $(".closing-button").prop('disabled', false);
                                    $(".closing-button").find(".loading-icon").hide();
                                    reverseButton.show();
                            } 
                    });    
			});
        
            selectedTabObj.find("[name=btnReverseClosingPeriod]").attr("id","btn-reverse-closing-"+selectedTabId);   
			$("#btn-reverse-closing-" + selectedTabId).bind( "click", function( event ) {    
                    $(".closing-button").prop('disabled', true);
                    $(this).find(".loading-icon").show();
			        $.ajax({
                            type: "POST",
                            url:  'ajax-coa.php', 
                            data : "action=reverseClosingMonthly",
                            success: function(data){   
                                    alert("Reverse tutup buku selesai");
                                    updateDataCOA(false);
                                    $(".closing-button").prop('disabled', false);
                                    $(".closing-button").find(".loading-icon").hide();
                            } 
                    });    
			});
        
        
            function openLockPeriodForm(){
                var arrParam=[];
                arrParam['url'] = 'openGLPeriodForm';
                loadPopup(arrParam);
            }
        
            function updateRevaluation(){
                 
                    $.ajax({
                            type: "POST",
                            url:  'ajax-coa.php', 
                            data : "action=updateRevaluation",
                            success: function(data){    
                                    data = JSON.parse(data);
                                    if(!data[0]['valid']){
                                        alert("Jurnal revaluasi berhasil diupdate");
                                    }else{
                                        alert(data[0]['message']);
                                    }
                                  
                            } 
                    });   
            }

            function updateRunningMonthLabel(){
                  $.ajax({
                            type: "POST",
                            url:  'ajax-coa.php', 
                            data : "action=getRunningMonth",
                            success: function(data){   
                                var data = JSON.parse(data);  
                                $( "#" + selectedTab.newPanel[0].id + " .running-month").html(data);
                            } 
                    });    
            }
        
        
            function updateReverseButton(){
                  $.ajax({
                            type: "POST",
                            url:  'ajax-coa.php', 
                            data : "action=getTotalClosedPeriod",
                            success: function(data){   
                                var data = JSON.parse(data);  
                                 
                                if (data == 0)
                                    $(".reverse-panel").hide();
                                else
                                    $(".reverse-panel").show();
                            } 
                    });    
            }
        
            function expandAll(){
                var expand = true;
                // cek kalo ad yg kebuka, tutup dulu semua 
                selectedTabObj.find(".data-record").each(function() { 
                            if ( $(this).hasClass("expand") ) {
                                expand = false;
                                return false;
                            }
                });
                
                if (expand){
                    selectedTabObj.find(".data-record").each(function() { 
                             $(this).addClass("expand").show();
                    });
                }else{  
                    selectedTabObj.find(".data-record").removeClass("expand");
                    selectedTabObj.find(".data-record").not("[relParentId=0]").each(function() { 
                             $(this).hide();
                    });
                }
                  
                
            }
        
            function updateDataCOA(loadMoreTriggered, selectedTabId ){    
                 var quickSearch = ""; 

                  if (selectedTabId == undefined){
                     selectedTabId = selectedTab.newPanel[0].id; 
                  }

                  phpDataListFile = tabParam[selectedTabId].phpDataListFile;
                  targetContent = $("#" + selectedTabId + " .data-list");


                    targetContent.html("<div style=\"clear:both; height:1em\"></div>" + _LOADING_ICON_);			 

                    //adding quick search value 
                    if ( $("[name=quick-search-" + selectedTabId+"]").val() != undefined)
                         quickSearch = $("[name=quick-search-" + selectedTabId+"]").val(); 


                    updateRunningMonthLabel();
                    updateReverseButton();
                
                   $.ajax({
                            type: "POST",
                            url:  phpDataListFile,
                            data: {generateDataRecords:1,
                                    quickSearchKey : quickSearch,  
                                    selectedCriteriaStatusKey :  tabParam[selectedTab.newPanel[0].id].selectedCriteriaStatusKey,
                                    selectedCriteriaTagKey :  tabParam[selectedTab.newPanel[0].id].selectedCriteriaTagKey, 
                                   } ,
                            success: function(data){  
                                
                                     var temp = JSON.parse(data); 

                                     tabParam[selectedTab.newPanel[0].id].statusInformation = temp.statusInformation;  
                                     tabParam[selectedTab.newPanel[0].id].tagInformation = temp.tagInformation;   
                                     tabParam[selectedTab.newPanel[0].id].contextMenu = temp.contextMenu;  
                                     tabParam[selectedTab.newPanel[0].id].contextMenuCallback = temp.contextMenuCallback;   

                                     targetContent.html(temp.dataList); 
                                     

                                     if (temp.eof)
                                            targetContent.find(".load-more:first").hide();
                                     else
                                            targetContent.find(".load-more:first").show();

                                     var loadMoreObj = targetContent.find(".load-more:first");
                                     loadMoreObj.find(".loading-icon:first").hide(); 
                                     loadMoreObj.click(function() {  
                                            $(this).unbind("click");  
                                            updateDataCOA(true)
                                      }); 

                                     tabParam[selectedTabId].lastRowIndex =  temp.lastRowIndex; 
 
                                     updateStatusPanel();
                                     updateRightClick(); 
                                     deselectAllRows();
                            } 
                    });    
            }

		  
	}); 
</script>

<div class="panel-data-list">    
 <div class="container">
    <div style="clear:both;"></div>
    <div class="action-bar-fixed user-select-none ">
        <div class="tab-title"></div> 
         
        <!-- NAVBAR --> 
        <div class="navbar navbar-default action-bar-navbar"><div id="navbar-collapse-grid" class="navbar-collapse collapse"> <ul class="nav navbar-nav"></ul> </div>  </div> 
         
        <div class="table-data-list">
            <div class="div-table-row">     
                 <?php	    
                for($j=0;$j<count($arrColumn);$j++){    
                    $width = (!empty( $arrColumn[$j]['width'])) ?  'width:' .$arrColumn[$j]['width'].'px;' : '';
                    $textAlign = (!empty( $arrColumn[$j]['align'])) ?  'text-align:' .$arrColumn[$j]['align'].';' : '';
                    $dbfield = $arrColumn[$j]['dbfield'];
                    $title = (!empty( $arrColumn[$j]['title'])) ?  $obj->lang[$arrColumn[$j]['title']]  : '';
                     
                    echo '<div style="'.$textAlign.' '.$width.' " class="div-table-col-5 col-header" relcol="'.$dbfield.'" reltype="-1">'.$title.'<div class="order-type"></div></div>';
                }
                ?>	  
                <div style="text-align:center; width: 30px;" class="div-table-col-5 col-header"> 
                </div>
            </div>
        </div>  
  
    </div> 
    <div class="data-list-margin" style="clear:both;"></div>
    <div class="data-list"></div>
 </div>    
</div>
