<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('JobApplication.class.php','JobOpportunities.class.php','CareerReference.class.php','JoiningConsideration.class.php'));

$jobApplication = createObjAndAddToCol( new JobApplication()); 
$jobOpportunities = createObjAndAddToCol( new JobOpportunities()); 
$careerReference = createObjAndAddToCol( new CareerReference()); 
$joiningConsideration =  createObjAndAddToCol( new JoiningConsideration()); 
    
$obj = $jobApplication;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'jobApplicationList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

$rsItemFile = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $_POST['trDate'] = $class->formatDBDate($rs[0]['createdon']);
    
    $rsJobOpportunities = $jobOpportunities->searchDataRow(array($jobOpportunities->tableName.'.pkey', $jobOpportunities->tableName.'.title'),
                                                      ' and '. $jobOpportunities->tableName.'.pkey = ' . $jobOpportunities->oDbCon->paramString($rs[0]['refjobopportunitykey'])
                                                      );
    $_POST['jobName'] = $rsJobOpportunities[0]['title'];

      //update file 
    $rsFile = array();  
    if( !empty($rs[0]['resumefile'])){
        $rsFile[0]['file'] =  $rs[0]['resumefile'];

        $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
        $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
        $obj->deleteAll($destinationPath); 

        if(!is_dir($destinationPath)) 
            mkdir ($destinationPath,  0755, true);

        $obj->fullCopy($sourcePath,$destinationPath); 
    }
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrSex = $obj->generateComboboxOpt(array('data' => $obj->getSex()));
$arrYear = $obj->generateYearSelectBox('',20,true);
$arrMonth = $obj->generateMonthSelectBox(true);
$rsReference = $careerReference->generateComboboxOpt(null, ' and '.$careerReference->tableName.'.statuskey = 1');  
$rsConsideration = $joiningConsideration->generateComboboxOpt(null, ' and '.$careerReference->tableName.'.statuskey = 1');  

$_POST['selStartMonth'] = $obj->formatDBDate($rs[0]['startdate'],'m');
$_POST['selStartYear'] = $obj->formatDBDate($rs[0]['startdate'],'yy');
$_POST['selEndtMonth'] = $obj->formatDBDate($rs[0]['enddate'],'m');
$_POST['selEndYear'] = $obj->formatDBDate($rs[0]['enddate'],'yy');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
        var jobApplication = new JobApplication(tabID,"<?php echo $obj->uploadResumeFolder; ?>",<?php echo json_encode($rsFile); ?>);

         prepareHandler(jobApplication);
 
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 
                                   name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.name[1]
                                            }, 
                                        }
                                    }, 
                                    email: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.email[1]
                                            }, 
                                            emailAddress: {
                                                message: phpErrorMsg.email[3]
                                            },
                                        }
                                    }, 
                                    phone: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.phone[1]
                                            },
                                        }
                                    }, 
                                    address: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.address[1]
                                            }, 
                                            
                                        }
                                    }, 
            
                                    jobName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.jobOpportunities[1]
                                            }, 
                                        }
                                    }, 

                                } ; 
            
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        
  	  
    }); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
    <?php prepareOnLoadDataForm($obj); ?>   
      
       <div class="div-table main-tab-table-2">
            <div class="div-table-row">
                <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOpportunities']); ?></label> 
                                         <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array(  
                                                                                'element' => array('value' => 'jobName',
                                                                                                   'key' => 'hidJobKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-job-opportunities.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '(1)' )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('email'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('phone'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selSex', $arrSex); ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('address', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
                             </div> 
                    </div>    
                    <div class="div-table-col"> 
                     <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['profile']); ?></div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['resume']); ?></label> 
                                <div class="col-xs-9">   
                                     <!-- image uploader --> 
                                            <div class="item-file-uploader">
                                                <ul class="file-list" ></ul>
                                                <div style="clear:both; height:1em; "></div>
                                                <div class="file-uploader">	
                                                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                                </div>
                                              </div>  
                                     <!-- image uploader -->
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['portfolio']); ?></label> 
                                <div class="col-xs-9" style="padding-top:7px">  
                                  <a href="<?php echo $rs[0]['portfoliourl'];?>" target="_blank"><?php echo $rs[0]['portfoliourl'];?></a>
                                </div> 
                            </div> 
                     </div>
                     <div class="div-tab-panel"> 
                            <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['experience']); ?></div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['latestRoleTitle']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('latestRole'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['latestCompany']); ?></label> 
                                <div class="col-xs-9">  
                                      <?php echo $obj->inputText('latestCompany'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                <div class="col-xs-9 flex">    
                                    <div><?php echo $obj->inputSelect('selStartMonth',$arrMonth); ?></div>
                                    <div><?php echo $obj->inputSelect('selStartYear',$arrYear); ?></div>
                                    <div class="consume"> - </div>
                                    <div><?php echo $obj->inputSelect('selEndMonth',$arrMonth); ?></div>
                                    <div><?php echo $obj->inputSelect('selEndYear',$arrYear); ?></div>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['iStillWorkHere']); ?></label> 
                                <div class="col-xs-9">  
                                      <?php echo $obj->inputCheckBox('chkStillWork'); ?>
                                </div> 
                            </div> 
                     </div>
                         <div class="div-tab-panel"> 
                            <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['otherInformation']); ?></div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['whereDidYouHearAboutUs']); ?></label> 
                                <div class="col-xs-9">  
                                      <?php echo $obj->inputSelect('selReference',$rsReference); ?>
                                </div> 
                            </div> 
                               <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['whatAreYourConsiderationsForJoiningOurCompany']); ?></label> 
                                <div class="col-xs-9">  
                                      <?php echo $obj->inputSelect('selConsideration',$rsConsideration); ?>
                                </div> 
                            </div> 
                     </div>
                </div>
            </div>
      </div>  
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
