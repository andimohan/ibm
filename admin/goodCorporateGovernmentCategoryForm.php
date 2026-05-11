<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('GoodCorporateGovernmentCategory.class.php'));
$goodCorporateGovernmentCategory = createObjAndAddToCol(new GoodCorporateGovernmentCategory());

$obj            = $goodCorporateGovernmentCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true))
   ;

$formAction = 'goodCorporateGovernmentCategoryList';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editCategoryCriteria = '';

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])) {
   $id = $_GET['id'];

   $_POST['name']        = $rs[0]['name'];
   $_POST['selCategory'] = $rs[0]['parentkey'];

   $arrChild = $obj->getChildren($rs[0]['pkey']);
   array_push($arrChild, $rs[0]['pkey']);
   if (!empty($arrChild))
      $editCategoryCriteria = ' and ' . $obj->tableName . '.pkey not in (' . implode(",", $arrChild) . ')';

}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

$arrCategory                = $obj->searchDataRow(
   array($obj->tableName . '.pkey', $obj->tableName . '.name'),
   ' and ' . $obj->tableName . '.statuskey = 1 ' . $editCategoryCriteria
);
$temp                       = count($arrCategory);
$arrCategory[$temp]['name'] = 'ROOT';
$arrCategory[$temp]['pkey'] = 0;

$arrCategory = $obj->generateComboboxOpt(array('data' => $arrCategory));


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

         //setOnDocumentReady(tabID);

         var goodCorporateGovernmentCategory = new GoodCorporateGovernmentCategory(tabID);

         prepareHandler(goodCorporateGovernmentCategory);

         var fieldValidation = {
            name: {
               validators: {
                  notEmpty: {
                     message: phpErrorMsg.category[1]
                  },
               }
            },
            code: {
               validators: {
                  notEmpty: {
                     message: phpErrorMsg.code[1]
                  },
               }
            }
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
                     <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
                           <?php echo ucwords($obj->lang['parent']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputSelect('selCategory', $arrCategory); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['category']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('name', array('multilang' => true)); ?>
                        </div>
                     </div>
                  </div>
               </div>
                
                 <div class="div-table-col">
                  <div class="div-tab-panel">
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                       <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?> 
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