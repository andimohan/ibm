<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('JobOpportunities.class.php'));

$jobOpportunities = createObjAndAddToCol( new JobOpportunities()); 
$careerDepartment = createObjAndAddToCol( new CareerDepartment()); 
$jobExperience = createObjAndAddToCol( new JobExperience()); 
$city = createObjAndAddToCol( new City()); 

$obj= $jobOpportunities;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'jobOpportunitiesList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
   

$rs = prepareOnLoadData($obj);

$rsItemImage = array(); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
      
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
     
    $editExperienceInactiveCriteria = ' or '.$jobExperience->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['experiencekey']); 
    $editDepartmentInactiveCriteria = ' or '.$careerDepartment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['departmentkey']); 
     
}  

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrExperience =  $jobExperience->generateComboboxOpt(null,array('criteria' => ' and ('.$jobExperience->tableName.'.statuskey = 1 ' .$editExperienceInactiveCriteria.')'));
$arrDepartment =  $careerDepartment->generateComboboxOpt(null,array('criteria' => ' and ('.$careerDepartment->tableName.'.statuskey = 1 ' .$editDepartmentInactiveCriteria.')'));
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
 
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var jobOpportunities = new JobOpportunities(tabID,"<?php echo $obj->uploadFolder; ?>","<?php echo $obj->uploadFileFolder; ?>",<?php echo json_encode($rsItemImage); ?>,<?php echo json_encode($rsItemFile); ?>);
    
        prepareHandler(jobOpportunities);   
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 
                                    title: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.jobOpportunities[1]
                                            }, 
                                        }
                                    }, 
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
        
    });
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?>     
        <?php echo $obj->generateLangOptions(); ?> 
      
        <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">
                    		<div class="div-tab-panel">  
                              	  <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div> 
                              
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('title',array('multilang' => true )); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['department']); ?></label> 
                                             <div class="col-xs-9">  
                                                      <?php echo  $obj->inputSelect('selDepartment', $arrDepartment); ?>
                                            </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobExperience']); ?></label> 
                                             <div class="col-xs-9">  
                                                  <?php echo  $obj->inputSelect('selExperience', $arrExperience); ?>
                                            </div> 
                                    </div>
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $city,
                                                                                    'element' => array('value' => 'cityName',
                                                                                                       'key' => 'hidCityKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-city.php',
                                                                                                        'data' => array('action' =>'searchData')
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featured']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputCheckBox('chkIsFeatured'); ?>   
                                        </div> 
                                    </div>
                                
<!--
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['url']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputText('url', array('etc' => 'placeholder="http://"')); ?>  
                                    </div> 
                                </div>
-->
                               
                                
                             </div>
                        
                    </div>
                    <div class="div-table-col">  
                            <div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                                    <div class="form-group">
                                     <div class="col-xs-12"> 
                                        <?php echo  $obj->inputTextArea('trDesc', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?> 
                                     </div>
                                </div>
                            </div>
                          
                    </div>    
               </div>
         </div>  
    <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                            <div class="form-group">
                                 <div class="col-xs-12"> 
                                     <?php echo  $obj->inputEditor('jobDesc',array('multilang' => true )); ?> 
                                 </div>
                            </div>
                        </div>
                  </div>
            </div>
      </div>     
      <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['requirement']); ?></div>
                            <div class="form-group">
                                 <div class="col-xs-12"> 
                                     <?php echo  $obj->inputEditor('reqDesc',array('multilang' => true )); ?> 
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