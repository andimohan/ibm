<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('NewRelease.class.php'));
$newRelease         = createObjAndAddToCol(new NewRelease());

$obj            = $newRelease;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true)) ;

$formAction = 'newReleaseList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$_POST['trPublishDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

$arrImages = array();
array_push($arrImages, array('key' => 'image', 'uploadFolder' => $obj->uploadFolder,'uploaderTarget' => 'cover-image-uploader'));
 
if (!empty($_GET['id'])) {
   $id = $_GET['id']; 
    
   $_POST['trDate']         = $obj->formatDBDate($rs[0]['trdate']);
   $_POST['publishDate']         = $obj->formatDBDate($rs[0]['publishdate']);


    foreach($arrImages as $index=>$imgRow){
        $key = $imgRow['key'];
        $uploadFolder = $imgRow['uploadFolder'];
        
        $rsImage = array();
        
        if( !empty($rs[0][$key])){
             
            $rsImage[0]['file'] =  $rs[0][$key];
            $rsImage[0]['phpthumbhash'] = getPHPThumbHash($rsImage[0]['file']); 

            $sourcePath = $obj->defaultDocUploadPath.$uploadFolder.$id; 

            $destinationPath = $obj->uploadTempDoc.$uploadFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }
        
        $arrImages[$index]['rsImage'] = $rsImage;

        
        // image detail
        $rsItemImage = $obj->getItemImages($id); 
    
        if(count($rsItemImage) > 0){
            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadDetailFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadDetailFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath);  
        }

        foreach($rsItemImage as $key=>$row) 
            $rsItemImage[$key]['phpthumbhash'] = getPHPThumbHash($row['file']);

    }
	

   // $rsDetailImage = $obj->getDetailWithRelatedInformation($id);

   // for ($i = 0; $i < count($rsDetailImage); $i++) {
   //    $rsDetailImage[$i]['phpthumbhash'] = getPHPThumbHash($rsDetailImage[$i]['file']);

   //     if(count($rsDetailImage) > 0){
   //          $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
   //          $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
   //          $obj->deleteAll($destinationPath); 

   //          if(!is_dir($destinationPath)) 
   //              mkdir ($destinationPath,  0755, true);

   //          $obj->fullCopy($sourcePath,$destinationPath);  
   //      }
   // }

}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

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
       
         var newRelease = new  NewRelease(tabID, <?php echo json_encode($arrImages); ?>,
                                          "<?php echo $obj->uploadDetailFolder; ?>",<?php echo json_encode($rsItemImage); ?> );

         prepareHandler(newRelease);

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
           
            orderList: {
                validators: { 
                    greaterThan: {
                        value: -1,
                        inclusive: false,
                        separator: ',', 
                        message: phpErrorMsg.orderList[2]
                    }
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
                           <?php echo ucwords($obj->lang['publishDate']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputDate('trPublishDate'); ?>
                        </div>
                     </div>


                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['title']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('title',array('multilang' => true )); ?>
                        </div>
                     </div>
                      
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['url']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('url'); ?>
                        </div>
                     </div>
                      
                      
                      <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['orderList']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputNumber('orderList'); ?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['coverImage']); ?>
                        </label>
                        <div class="col-xs-9">
                           <div class="item-image-uploader cover-image-uploader">
                              <ul class="image-list"></ul>
                              <div style="clear:both; height:1em; "></div>
                              <div class="file-uploader">
                                 <noscript>
                                    <p>Please enable JavaScript to use file uploader.</p>
                                 </noscript>
                              </div>
                           </div>
                        </div>
                     </div>
                      
                      
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['image']); ?>
                        </label>
                        <div class="col-xs-9">
                           <div class="item-image-uploader item-detail-image-uploader">
                              <ul class="image-list"></ul>
                              <div style="clear:both; height:1em; "></div>
                              <div class="file-uploader">
                                 <noscript>
                                    <p>Please enable JavaScript to use file uploader.</p>
                                 </noscript>
                              </div>
                           </div>
                        </div>
                     </div>

                  </div>
               </div>

               <div class="div-table-col">
                  <div class="div-tab-panel">
                     <div class="div-table-caption border-green">
                        <?php echo ucwords($obj->lang['shortDescription']); ?>
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <?php echo $obj->inputTextArea('txtShortDescription', array('etc' => 'style="height:10em;"', 'multilang' => true )); ?>
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
                     <div class="div-table-caption border-blue">
                        <?php echo ucwords($obj->lang['description']); ?>
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <?php echo $obj->inputEditor('txtDescription', array ('multilang' => true )); ?>
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