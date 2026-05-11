<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodCorporateGovernmentReport.class.php','Category.class.php','GoodCorporateGovernmentCategory.class.php'));
$gcgReport = createObjAndAddToCol(new GoodCorporateGovernmentReport());
$gcgCategory = createObjAndAddToCol(new GoodCorporateGovernmentCategory());

$obj            = $gcgReport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'goodCorporateGovernmentReportList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
   $id = $_GET['id'];
    
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
$arrYear = $class->generateYearSelectBox('', 10);
$arrYear = array_reverse($arrYear,true);
$arrCat = $gcgCategory->generateComboboxOpt(null,array('criteria' =>' and '.$gcgCategory->tableName.'.statuskey = 1 and  '.$gcgCategory->tableName.'.isleaf = 1 ' ));
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

         var goodCorporateGovernmentReport = new GoodCorporateGovernmentReport(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>,"<?php echo $obj->uploadFileFolder; ?>",<?php echo json_encode($rsFile); ?>);

         prepareHandler(goodCorporateGovernmentReport);

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
                           <?php echo ucwords($obj->lang['date']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputDate('trDate'); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['category']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $class->inputSelect('selCategory', $arrCat); ?>
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
                           <?php echo ucwords($obj->lang['alwaysShown']); ?>
                        </label>
                        <div class="col-xs-3">
                           <?php echo $class->inputCheckBox('chkAlwaysShow'); ?>
                        </div>
                         <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['tableFormat']); ?>
                        </label>
                        <div class="col-xs-3">
                           <?php echo $class->inputCheckBox('chkTableFormat'); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['period']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $class->inputSelect('selYearPeriod', $arrYear); ?>
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
                      
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['widget']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputTextArea('txtWidget', array( 'etc' => 'style="height:10em;"')); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['shortDescription']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputTextArea('txtShortDescription', array('multilang' => true, 'etc' => 'style="height:10em;"')); ?>
                        </div>
                     </div>
 
                  </div>
               </div>

               <div class="div-table-col">
                  <div class="div-tab-panel">
                     <div class="div-table-caption border-green">
                        <?php echo ucwords($obj->lang['description']); ?>
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <?php echo $obj->inputEditor('txtDescription', array('multilang' => true)); ?>
                        </div>
                     </div>
                  </div>
               </div>
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