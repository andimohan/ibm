<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('AssetItem.class.php'));
$assetItem = createObjAndAddToCol(new AssetItem());
$assetDepreciation = createObjAndAddToCol(new AssetDepreciation());
$warehouse = createObjAndAddToCol(new Warehouse());
$brand = createObjAndAddToCol(new Brand());
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());
$categoryAssetItem = createObjAndAddToCol(new CategoryAssetItem());
$carSeries = createObjAndAddToCol(new CarSeries());
$assetItemCOGSAdjustment = createObjAndAddToCol(new AssetItemCOGSAdjustment());
$assetItemMovement = createObjAndAddToCol(new AssetItemMovement());

$obj = $assetItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'assetItemList';
$editWarehouseInactiveCriteria = '';
$editGroupAssetInactiveCriteria = '';

$display = 'display:none';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj);
$rsDetail = array();

$emptyDate = '00 / 00 / 0000';
$_POST['expLicenseDate'] = $emptyDate;

$dateReturnOnEmpty = array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000');


if (!empty($_GET['id'])) {
   $id = $_GET['id'];

   $_POST['aging'] = $rsCategory[0]['aging'];
   // $_POST['selType'] = $rsCategory[0]['typekey'];
   // $_POST['selCategory'] = $rsCategory[0]['categorykey'];
	
    $_POST['hidBrandKey'] = $rs[0]['brandkey']; 
    if (!empty($rs[0]['brandkey'])){
		$rsBrand = $brand->getDataRowById($rs[0]['brandkey']);
		$_POST['brandName'] = $rsBrand[0]['name'];
	}
    
    $rsPurchase = $purchaseOrder->getDataRowById($rs[0]['refpurchasekey']);
    if (!empty($rs[0]['refpurchasekey'])){
        $_POST['refCode'] = $rsPurchase[0]['code'];
    }

   $_POST['serialNumber'] = $rs[0]['serialnumber'];

   $_POST['selCapacity'] = $rs[0]['capacitykey'];
   $_POST['capacity'] = $obj->formatNumber($rs[0]['capacity']);

   $_POST['selMast'] = $rs[0]['mastkey'];
   $_POST['mast'] = $obj->formatNumber($rs[0]['mast']);

   $_POST['chassisNumber'] = $rs[0]['chassisnumber'];
   $_POST['year'] = $rs[0]['year'];
   $_POST['itemCondition'] = $rs[0]['itemcondition'];
   $_POST['note'] = $rs[0]['note'];
   $_POST['hidCurrrentValue'] = $rs[0]['name'].'-'.$rs[0]['serialnumber'].'-'.$rs[0]['typekey'].'-'.$rs[0]['brandkey'].'-'.$rs[0]['assetcategorykey'];


   if(!empty($rs[0]['assetcategorykey'])) {
      $rsCategoryAssetItem = $categoryAssetItem->getDataRowById($rs[0]['assetcategorykey']);

      $_POST['hidCategoryKey'] = $rsCategoryAssetItem[0]['pkey'];
      $categoryName =  $categoryAssetItem->getPath($rsCategoryAssetItem[0]['pkey']);
      $_POST['categoryName'] = $categoryName[0]['path'];
   }

   if(!empty($rs[0]['typekey'])) {
      $rsCarSeries = $carSeries->getDataRowById($rs[0]['typekey']);

      $_POST['typeName'] = $rsCarSeries[0]['name'];
      $_POST['hidTypeKey'] = $rsCarSeries[0]['pkey'];
   }

   
   $_POST['expLicenseDate'] = $obj->formatDBDate($rs[0]['explicensedate'], 'd / m / Y', $dateReturnOnEmpty);


   $editWarehouseInactiveCriteria = ' or ' . $warehouse->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1' . $editWarehouseInactiveCriteria . ')'), 'pkey', 'name');
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWeight = $obj->generateComboboxOpt(array('data' => $obj->getSystemWeight()));
$arrMast = array();
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
         
         var assetItem = new AssetItem(tabID);

         prepareHandler(assetItem);

         var fieldValidation = {
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
         <?php echo $obj->inputHidden('hidCurrrentValue'); ?>

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
                           <?php echo ucwords($obj->lang['warehouse']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse, array('readonly' => true)); ?>
                        </div>
                     </div>
                      <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['refCode']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('refCode', array('readonly' => true)); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['name']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('name'); ?>
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['serialNumber']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('serialNumber'); ?>
                        </div>
                     </div>
   
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['category']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputAutoComplete(
                              array(
                                 'objRefer' => $categoryAssetItem,
                                 'revalidateField' => true,
                                 'element' => array(
                                    'value' => 'categoryName',
                                    'key' => 'hidCategoryKey'
                                 ),
                                 'source' => array(
                                    'url' => 'ajax-category-asset-item.php',
                                    'data' => array('action' => 'searchData', 'isleaf' => 1 )
                                 )
                              )
                           ); ?>
                        </div>
                     </div>

                      <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['type']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputAutoComplete(
                              array(
                                 'objRefer' => $carSeries,
                                 'element' => array(
                                    'value' => 'typeName',
                                    'key' => 'hidTypeKey'
                                 ),
                                 'source' => array(
                                    'url' => 'ajax-car-series.php',
                                    'data' => array('action' => 'searchData')
                                 ),
                                 'popupForm' => $popupOpt
                              )
                           ); ?>
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['brand']); ?></label> 
                        <div class="col-xs-9">  
                           <?php    
                              echo $obj->inputAutoComplete(array( 
                                    'objRefer' => $brand,  
                                    'element' => array('value' => 'brandName',
                                                   'key' => 'hidBrandKey'),
                                    'source' =>array(
                                                   'url' => 'ajax-brand.php',
                                                   'data' => array(  'action' =>'searchData' )
                                                ) ,
                                    'popupForm' => array(
                                                      'url' => 'brandForm.php',
                                                      'element' => array('value' => 'brandName',
                                                      'key' => 'hidBrandKey'),
                                                      'width' => '600px',
                                                      'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['brand'])
                                                   ),
                                    'callbackFunction' => 'getTabObj().updateMPBrandAttribute()'  
                                    )
                              );  
                           ?> 
                        </div> 
                     </div>

                     <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['chassisNumber']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('chassisNumber'); ?>
                        </div>
                     </div>

                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['year']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('year'); ?>
                        </div>
                     </div>

                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemCondition']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputText('itemCondition'); ?>
                        </div>
                     </div>
                     
                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['capacity']); ?>
                        </label>
                        <div class="col-xs-9">
                           <div class="flex">
                              <div>
                                 <?php echo $obj->inputSelect('selCapacity', $arrWeight); ?>
                              </div>
                              <div class="consume">
                                 <?php echo $obj->inputDecimal('capacity'); ?>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mast']); ?>
                        </label>
                        <div class="col-xs-9">
                           <div class="flex">
                              <div>
                                 <?php echo $obj->inputSelect('selMast', $arrMast); ?>
                              </div>
                              <div class="consume">
                                 <?php echo $obj->inputDecimal('mast'); ?>
                              </div>
                           </div>
                        </div>
                     </div>

                     <!-- <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['capacity']); ?> / <?php echo ucwords($obj->lang['measurement']); ?>
                        </label>
                        <div class="col-xs-9">
                            <div class="flex">
                              <div class="consume" >
                                 <?php echo $obj->inputDecimal('capacity'); ?>
                              </div>
                              <div class="text-muted">Ton</div>
                              <div ></div>
                              <div class="consume" >
                                 <?php echo $obj->inputDecimal('mast'); ?>
                              </div>
                              <div class="text-muted">meter</div>
                           </div>                        
                          </div>
                     </div> -->
                     <div class="form-group" style="<?php echo $display; ?>">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['expirationDate']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputDate('expLicenseDate', array('allowEmpty' => true)); ?>
                        </div>
                     </div>
                     <div class="form-group" style="<?php echo $display; ?>">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['usefulLife']); ?> (
                           <?php echo $obj->lang['year']; ?>)
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputNumber('aging', array('readonly' => true)); ?>
                        </div>
                     </div>

                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['acquisitionDate']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputDate('acquisitionDate'); ?>
                        </div>
                     </div>

                     <div class="form-group" style="<?php echo $display; ?>">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['acquisitionValue']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputNumber('acquisitionValue'); ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-xs-3 control-label">
                           <?php echo ucwords($obj->lang['hpp']); ?>
                        </label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputNumber('bookValue', array('readonly' => true)); ?>
                        </div>
                     </div>
                     <!--
                     <div class="form-group">
                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['residue']); ?></label>
                        <div class="col-xs-9">
                           <?php echo $obj->inputNumber('residue', array('etc' => 'style="text-align:right" ')); ?>
                        </div>
                     </div>
-->

                  </div>
               </div>
               <div class="div-table-col">
                  <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?>
                     </div>
                     <div class="form-group">
                        <div class="col-xs-12">
                           <?php echo $obj->inputTextArea('note', array('etc' => 'style="height:10em;"')); ?>
                        </div>
                     </div>
                  </div>
                  
                  <div class="div-tab-panel">
                     <div class="div-table-caption border-orange">
                        <?php echo ucwords($obj->lang['cogsAdjustment']); ?>
                     </div>

                     <div class="div-table" style="width:100%">
                        <div class="div-table-row">
                           <!-- <div class="div-table-col-5"
                              style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;">
                              <strong>
                                 <?php echo ucwords($obj->lang['transactionCode']); ?>
                              </strong>
                           </div> -->
                           <div class="div-table-col-5"
                              style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center">
                              <strong>
                                 <?php echo ucwords($obj->lang['date']); ?>
                              </strong>
                           </div>
                           <div class="div-table-col-5"
                              style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:left">
                              <strong>
                                 <?php echo ucwords($obj->lang['description']); ?>
                              </strong>
                           </div>
                           <div class="div-table-col-5"
                              style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;">
                              <strong>
                                 <?php echo ucwords($obj->lang['amount']); ?>
                              </strong>
                           </div>
                        </div>

                        <?php
                           if (!empty($_GET['id'])) {
                              // $rsCOGSAdjustment = $assetItemCOGSAdjustment->getDetailCOGSAdjustmentByAssetItem($_GET['id']);
                              $rsCOGSAdjustment = $assetItemMovement->searchData('', '', true, ' and ' . $assetItemMovement->tableName . '.statuskey = 1 and itemkey = ' . $assetItemMovement->oDbCon->paramString($_GET['id']));
                              for ($i = 0; $i < count($rsCOGSAdjustment); $i++) {
                                 if ($rsCOGSAdjustment[$i]['costinbaseunit'] == 0) continue;
                                 // if ($rsCOGSAdjustmentData[0]['statuskey'] == 2 || $rsCOGSAdjustmentData[0]['statuskey'] == 3) {
                                       echo '
                                                <div class="div-table-row"> 
                                                   <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                      ' . $obj->formatDBDate($rsCOGSAdjustment[$i]['trdate']) . '
                                                   </div> 
                                                   <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:left" > 
                                                      ' . $rsCOGSAdjustment[$i]['note'] . '
                                                   </div> 
                                                   <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                       '. $obj->formatNumber($rsCOGSAdjustment[$i]['costinbaseunit']) . '</span>
                                                   </div>
                                                </div> 
                                          ';
                                 //}
                              }
                           }
                        ?>

                     </div>

                  </div>
                  
               </div>
            </div>
         </div>
         <div class="form-button-margin"></div>
         <div class="form-button-panel">
            <?php echo $obj->generateSaveButton(); ?>
         </div>

      </form>
      <?php echo $obj->showDataHistory(); ?>
   </div>

</body>

</html>