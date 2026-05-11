<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodCorporateGovernment.class.php', 'GoodCorporateGovernmentCategory.class.php'));
$goodCorporateGovernment         = createObjAndAddToCol(new GoodCorporateGovernment());
$goodCorporateGovernmentCategory = createObjAndAddToCol(new GoodCorporateGovernmentCategory());

$obj            = $goodCorporateGovernment;
$securityObject = $obj->securityObject;

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'goodCorporateGovernmentList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;


$rs = prepareOnLoadData($obj);

$rsGoodCorporateGovernmentReport = array();
$rsGoodCorporateGovernmentTeam = array();
$rsFile = array();
$rsItemImage = array();

if (!empty($_GET['id'])) {
   $id = $_GET['id'];
 
   $rsGoodCorporateGovernmentReport = $obj->getGoodCorporateGovernmentReportDetail($id);
   $rsGoodCorporateGovernmentTeam = $obj->getGoodCorporateGovernmentTeam($id);

   $_POST['title'] = $rs[0]['title'];

   $_POST['hidCategoryKey'] = $rs[0]['categorykey'];
   if (!empty($rs[0]['categorykey'])) {
      $rsCategory            = $goodCorporateGovernmentCategory->getDataRowById($rs[0]['categorykey']);
      $categoryName          = $goodCorporateGovernmentCategory->getPath($rsCategory[0]['pkey']);
      $_POST['categoryName'] = $categoryName[0]['path'];
   }
 
    $rsItemImage = array();  
	if( !empty($rs[0]['image'])){
		$rsItemImage[0]['file'] =  $rs[0]['image'];
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
    
 	$rsFile = array();  
	if( !empty($rs[0]['file'])){
		$rsFile[0]['file'] =  $rs[0]['file'];
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}


}
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrYear   = $class->generateYearSelectBox('', 10);

?>
<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title></title>


   <script type="text/javascript">
      jQuery(document).ready(function () {
         var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>

         var goodCorporateGovernment = new GoodCorporateGovernment(tabID, <?php echo json_encode(array(
                                                    'rsGoodCorporateGovernmentReport' => $rsGoodCorporateGovernmentReport,
                                                    'rsGoodCorporateGovernmentTeam' => $rsGoodCorporateGovernmentTeam,
                                                )); ?>,
                                                  "<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>, 
                                                  "<?php echo $obj->uploadFileFolder; ?>",<?php echo json_encode($rsFile); ?>             
                                                );
           

         prepareHandler(goodCorporateGovernment);

         var fieldValidation = {
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
                     message: phpErrorMsg.title[1]
                  },
               }
            },
         };

         setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);


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
                     <div class="div-table-caption border-orange">
                        <?php echo ucwords($obj->lang['generalInformation']); ?>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['status']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputSelect('selStatus', $arrStatus); ?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['code']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputAutoCode('code'); ?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['title']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('title', array('multilang' => true)); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['category']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php
                           echo $obj->inputAutoComplete(
                              array(
                                 
                                 'element'   => array(
                                    'value' => 'categoryName',
                                    'key'   => 'hidCategoryKey'
                                 ),
                                 'source'    => array(
                                    'url'  => 'ajax-good-corporate-government-category.php',
                                    'data' => array('action' => 'searchData', 'isleaf' => 1)
                                 )
                              )
                           );
                           ?>
                        </div>
                     </div>
                      
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                            <div class="col-xs-9"> 
                                 <!-- image uploader --> 
                                <div class="item-image-uploader">
                                    <ul class="image-list" ></ul>
                                    <div style="clear:both; height:1em; "></div>
                                    <div class="file-uploader">	
                                        <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                    </div>
                                  </div>  
                                <!-- image uploader --> 
                            </div> 
                        </div>   
                    <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['file']); ?></label> 
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
<!--
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['shortDescription']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('txtShortDescription', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
                        </div>
                     </div>
-->
                  </div>
               </div>

               <div class="div-table-col">
                  <div class="div-tab-panel">
                     <div class="div-table-caption border-green">
                        <?php echo ucwords($obj->lang['shortDescription']); ?>
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <?php echo $obj->inputTextArea('txtShortDescription', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
<!--                           <?php // echo $obj->inputEditor('txtDescription', array('multilang' => true)); ?>-->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         
         <div class="div-tab-panel">
            <div class="div-table-caption border-red"> <?php echo ucwords($obj->lang['report']); ?> </div>
         
            <div class="div-table mnv-transaction mnv-job transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                  <div class="div-table-row">
                     <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['name']); ?>
                  </div> 
                  <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>">
                  </div>
                  <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>">
                  </div>
               </div>
            
               <?php
               $goodCorporateGovernmentReport = count($rsGoodCorporateGovernmentReport);
            
               for ($i = 0; $i <= $goodCorporateGovernmentReport; $i++) {

                  $class     = 'transaction-detail-row';
                  $overwrite = true;
                  $disabled  = false;
                  $etc       = '';

                  if ($i == $goodCorporateGovernmentReport) {
                     $class     = 'good-corporate-government-report-row-template row-template';
                     $overwrite = false;
                     $disabled  = true;
                     $etc       = 'disabled="disabled"';
                  } else {
                     $_POST['hidDetailKey[]'] = $rsGoodCorporateGovernmentReport[$i]['pkey'];
                     $_POST['hidReportKey[]']    = $rsGoodCorporateGovernmentReport[$i]['reportkey']; 
                     $_POST['reportName[]']    = $rsGoodCorporateGovernmentReport[$i]['reporttitle']; 
                  }
                  ?>
            
                  <div class="div-table-row <?php echo $class; ?>" >
                     <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidReportKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputText('reportName[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left" ' . $etc, 'allowedStatusForEdit' => array(1))); ?>
                     </div> 
                     <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="good-corporate-government-report-row-template"')); ?></div>
                     <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')); ?></div>
                  </div>
            
               <?php } ?>
            </div>

         </div>

         <div class="div-tab-panel">
            <div class="div-table-caption border-blue">  <?php echo ucwords($obj->lang['managementTeam']); ?> </div>
         
            <div class="div-table mnv-transaction mnv-job transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
               <div class="div-table-row">
                  <div class="div-table-col detail-col-header">
                     <?php echo ucwords($obj->lang['name']); ?>
                  </div>
                   <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" >
                     </div>
                     <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"
                     style="width:35px;">
                  </div>
               </div>
         
               <?php
               $goodCorporateGovernmentTeam = count($rsGoodCorporateGovernmentTeam);

               for ($i = 0; $i <= $goodCorporateGovernmentTeam; $i++) {

                  $class     = 'transaction-detail-row';
                  $overwrite = true;
                  $disabled  = false;
                  $etc       = '';
                  if ($i == $goodCorporateGovernmentTeam) {
                     $class     = 'good-corporate-government-team-row-template row-template';
                     $overwrite = false;
                     $disabled  = true;
                     $etc = 'disabled="disabled"';
                  } else {
                     $_POST['hidGoodCorporateGovernmentTeamKey[]'] = $rsGoodCorporateGovernmentTeam[$i]['pkey'];
                     $_POST['teamName[]']                          = $rsGoodCorporateGovernmentTeam[$i]['name']; 
                     $_POST['hidRefTeamKey[]'] = $rsGoodCorporateGovernmentTeam[$i]['refteamkey']; 
                  }
                  ?>
         
                  <div class="div-table-row <?php echo $class; ?>" style="">
                     <div class="div-table-col detail-col-detail" style="vertical-align:top">
                        <?php echo $obj->inputHidden('hidGoodCorporateGovernmentTeamKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidRefTeamKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                         <?php echo $obj->inputText('teamName[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                     </div>
                     <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="good-corporate-government-team-row-template"')); ?>
                     </div>
                     <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')); ?></div>
                  </div>
         
               <?php } ?>
            </div>
         
         </div>
         

         <div class="form-button-panel">
            <?php echo $obj->generateSaveButton(); ?>
         </div>
      </form>

      <?php echo $obj->showDataHistory(); ?>
   </div>
</body>

</html>