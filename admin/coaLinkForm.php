<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('COALink.class.php');
$coaLink = createObjAndAddToCol(new COALink());

$obj= $coaLink;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$_POST['action'] = 'edit';

$formAction = 'coaLinkList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

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
      .bootstrapValidator().on('success.form.bv', function(e) {
      
            // Get the form instance
             var $form = $(e.target);

             var btnSave = $form.find("[name=btnSave]");
  
             btnSave.prop('disabled', true);
             btnSave.find(".loading-icon").show();

            // Prevent form submission
            e.preventDefault();
 
            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data 
       $.post($form.attr('action'), $form.serialize(), function(result) {  
            alert("Pengaturan COA Link berhasil disimpan.");
            selectedTab.newTab[0].remove();
            $tabs.tabs("refresh");   
                }, 'json');
        });
    
});
  
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
    <?php echo $obj->inputHidden('action'); ?>
      <?php
                        $popupOpt =  (!$isQuickAdd) ? array(
                                            'url' => 'chartOfAccountForm.php',
                                            'element' => array('value' => 'coalink[]',
                                                   'key' => 'hidcoakey[]'),
                                            'width' => '600px',
                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount'])
                                        )  : ''; 
    
                        $arrAutoComplete = array(
                                                'objRefer' => $chartOfAccount,
                                                'revalidateField' => false, 
                                                'element' => array('value' => 'coalink[]',
                                                                   'key' => 'hidcoakey[]'),
                                                'source' =>array(
                                                                    'url' => 'ajax-coa.php',
                                                                    'data' => array(  'action' =>'searchData' )
                                                                ) ,
                                                'popupForm' => $popupOpt
                                    ); 
      ?>
      
    
         <div class="div-table main-tab-table-1">
            <div class="div-table-row">
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 

                         <?php
                          // laba tahun berjalan

                              $rsCOALink = $obj->getCOALink ('retainedearnings');
                              $_POST['hidcoakey[]'] = '';
                              $_POST['coalink[]'] = '';

                              if (!empty($rsCOALink)){ 
                                  $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                  $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                              }  

                          ?>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['retainedEarning']; ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputHidden('hidrefkey[]'); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]', array('value' => 'retainedearnings')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                            </div> 
                        </div> 
                        
                         <?php
                              // laba tahun berjalan

                                  $rsCOALink = $obj->getCOALink ('currentyearearnings');
                                  $_POST['hidcoakey[]'] = '';
                                  $_POST['coalink[]'] = '';

                                  if (!empty($rsCOALink)){ 
                                      $_POST['hidcoakey[]'] = $rsCOALink[0]['coakey']; 
                                      $_POST['coalink[]'] = $rsCOALink[0]['value']; 
                                  }  

                          ?> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['currentEarning']; ?></label> 
                            <div class="col-xs-9"> 
                                
                                <?php echo $obj->inputHidden('hidrefkey[]'); ?> 
                                <?php echo $obj->inputHidden('hidcategorykey[]', array('value' => 'currentyearearnings')); ?>  
                                <?php echo $obj->inputAutoComplete($arrAutoComplete); ?>     
                                  
                            </div> 
                        </div> 
                        
                    </div> 
                </div>
             </div>
        </div>      
       
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>