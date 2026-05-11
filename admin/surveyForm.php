<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $survey;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'surveyList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$rsReferralSurveyDetail = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$rs = $obj->getDataRowById($id);
    
	$rsReferralSurveyDetail = $obj->getDetailById($id);
	
	$_POST['hidId'] = $rs[0]['pkey'];
	$_POST['code'] = $rs[0]['code'];
	$_POST['selStatus'] = $rs[0]['statuskey'];  
	$_POST['question'] = $rs[0]['question']; 
	 
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
                            message: 'Kode harus diisi.'
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
		
		// DETAIL CLONE
		 $("#defaultForm-"+selectedTab.newPanel[0].id+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("referral-survey-row-template");
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
    
        <div class="div-table-tab-form" style="margin:auto; width:600px;">
		
              <div class="div-table-row form-group">
                <div class="div-table-col-5 div-table-col-header">
                    <label class="col-lg-1 control-label">Status</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <?php echo  $obj->inputSelect('selStatus', $arrStatus, true); ?>
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
                    <label class="col-lg-1 control-label">Pertanyaan</label>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                          <?php echo $obj->input('text','question'); ?>
                    </div> 
                </div>
					
             </div>
             
               <div class="div-table-row form-group">
                <div class="div-table-col-5" >
                    <label class="col-lg-1 control-label">Pilihan Jawaban</label>
                    <?php
						echo '<script>';
						
						for ($i=0;$i<count($rsReferralSurveyDetail); $i++){
							
							if ($i==0){
							$_POST['answer[]'] = $rsReferralSurveyDetail[$i]['answer'];
							}else{
								$arrPostValue = array();
								array_push($arrPostValue,array("selector" => 'answer', "value" =>   $rsReferralSurveyDetail[$i]['answer'])) ;
								echo 'addNewTemplateRow("referral-survey-row-template",\''.str_replace("'","\'",json_encode($arrPostValue)).'\'); ';
							}
						}
						
						echo '</script>'; 
					 ?>
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                          <?php echo $obj->input('text','answer[]',true,'',''); ?>
                    </div> 
                </div>
					
             </div>
             
              <!-- Template for dynamic field -->  
             <div class="div-table-row form-group referral-survey-row-template" style="display:none;"  > 
                <div class="div-table-col-5"> 
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                         <div style="width:87%; float:left"><?php echo $obj->input('text','answer[]',false,'','disabled="disabled"',''); ?></div>
                         <div style="loat:left"><?php echo $obj->input('button','btnDeleteRows',false,$obj->lang['delete'],'','btn btn-link remove-button'); ?></div>
                    </div>
                </div>  
              </div> 
			 
              <div class="div-table-row form-group" > 
                <div class="div-table-col-5"> 
                </div> 
                <div class="div-table-col-5">
                    <div class="col-lg-12"> 
                        <?php echo $obj->input('button','btnAddRows',false,'Tambah Jawaban','style="margin-top:0.2em;"'); ?>
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