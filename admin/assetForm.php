<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('Asset.class.php'));
$asset = createObjAndAddToCol(new Asset());
$assetCategory = createObjAndAddToCol(new AssetCategory());
$assetGroup = createObjAndAddToCol(new AssetGroup());
$assetDepreciation = createObjAndAddToCol(new AssetDepreciation());
$warehouse = createObjAndAddToCol( new Warehouse()); 

$obj = $asset;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$formAction = 'assetList';
$editWarehouseInactiveCriteria='';
$editGroupAssetInactiveCriteria='';

$isQuickAdd = (isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj);
$rsDetail = array();

$emptyDate = '00 / 00 / 0000';
$_POST['expLicenseDate'] = $emptyDate;

$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');


if (!empty($_GET['id'])) {
	$id = $_GET['id'];
	
	$rsCategory = $assetCategory->getDataRowById($rs[0]['categorykey']);
	
	$_POST['aging'] = $rsCategory[0]['aging'];
	$_POST['selType'] = $rsCategory[0]['typekey'];
    
    
    $_POST['expLicenseDate'] = $obj->formatDBDate($rs[0]['explicensedate'],'d / m / Y', $dateReturnOnEmpty);  

	
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	$editGroupAssetInactiveCriteria = ' or '.$assetGroup->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrCategory = $obj->generateComboboxOpt(array('data' => $assetCategory->searchDataRow(array($assetCategory->tableName . '.pkey', $assetCategory->tableName . '.name', $assetCategory->tableName . '.aging', $assetCategory->tableName . '.typekey'))),'','',array('rel-aging' => 'aging','rel-type' => 'typekey'));
$arrAssetGroup = $obj->generateComboboxOpt(array('data' => $assetGroup->searchDataRow(array($assetGroup->tableName . '.pkey', $assetGroup->tableName . '.name'))));
$arrAssetType = $obj->generateComboboxOpt(array('data' => $obj->getAssetType()));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>

	<script type="text/javascript">
		jQuery(document).ready(function() {
 
		var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var asset  = new Asset(tabID);
    
        prepareHandler(asset);   
        
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

			<div class="div-table main-tab-table-2">
				<div class="div-table-row">
					<div class="div-table-col">
						<div class="div-tab-panel">
							<div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
								<div class="col-xs-9">
									<?php echo  $obj->inputSelect('selStatus', $arrStatus);  ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputAutoCode('code'); ?>
								</div>
							</div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                </div>
                            </div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputText('name'); ?>
								</div>
							</div>
                            
							<div class="form-group">
								<label class="col-xs-3 control-label">
									<?php echo ucwords($obj->lang['category']); ?> / <?php echo ucwords($obj->lang['type']); ?>
								</label>
								<div class="col-xs-9">
									<div class="flex">
										<div class="consume"><?php echo  $obj->inputSelect('selCategory', $arrCategory); ?></div>
										<div>/</div>
										<div class="consume"><?php echo  $obj->inputSelect('selType', $arrAssetType);  ?></div> 
									</div>
								</div>
							</div> 
							 <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['assetGroup']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputSelect('selAssetGroup', $arrAssetGroup); ?>
                                </div>
                            </div>
                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['expirationDate']); ?></label>
								<div class="col-xs-9">
                                    <?php echo $obj->inputDate('expLicenseDate', array('allowEmpty' => true)); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['usefulLife']); ?> (<?php echo $obj->lang['year']; ?>)</label>
								<div class="col-xs-9">
									<?php echo $obj->inputNumber('aging', array('readonly' => true)); ?>
								</div>
							</div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['acquisitionDate']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDate('acquisitionDate'); ?>
                                </div>
                            </div>
							
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['acquisitionValue']); ?></label>
								<div class="col-xs-9">
									<?php echo $obj->inputNumber('acquisitionValue'); ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookValue']); ?></label>
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
                                  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['depreciation']); ?></div>
                                   <div class="div-table" style="width:100%">
                                        <div class="div-table-row"> 
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:150px;" > 
                                                <strong><?php echo ucwords($obj->lang['transactionCode']); ?></strong>
                                             </div>
                                            <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center" > 
                                                <strong><?php echo ucwords($obj->lang['date']); ?></strong>
                                             </div>
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > 
                                                <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                             </div>
    
                                        </div> 
                                             <?php 
                                             if (!empty($_GET['id'])){
                                                  $rsDetailDepreciation = $assetDepreciation->getDetailDepreciationByAsset($_GET['id']);
                                                  for ($i=0;$i<count($rsDetailDepreciation);$i++){
                                                      $rsAssetDepreciation= $assetDepreciation->getDataRowById($rsDetailDepreciation[$i]['refkey']);
                                                      if($rsAssetDepreciation[0]['statuskey'] == 2 || $rsAssetDepreciation[0]['statuskey'] == 3){
                                                          echo '
                                                             <div class="div-table-row"> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                    '.$rsAssetDepreciation[0]['code'].'
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                                    '.$obj->formatDBDate($rsAssetDepreciation[0]['trdate']).'
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                                    '.$obj->formatNumber($rsDetailDepreciation[$i]['value']).'
                                                                 </div>
                                                             </div> 
                                                         '; 
                                                      }
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
