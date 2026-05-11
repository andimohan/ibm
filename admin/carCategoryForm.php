<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Category.class.php','CarCategory.class.php','Item.class.php'));
$carCategory = createObjAndAddToCol(new CarCategory()); 
$itemPosition = createObjAndAddToCol(new ItemPosition());
$item = createObjAndAddToCol(new Item());

$obj= $carCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'carCategoryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$rs = prepareOnLoadData($obj);

$arrItemPosition = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];

    $carcategorykey = $rs[0]['pkey'];

    $_POST['name'] = $rs[0]['name']; 
    $_POST['trShortDesc'] = $rs[0]['shortdescription']; 
    $_POST['orderList'] = $rs[0]['orderlist']; 

} else {
    $carcategorykey = 1;
}


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');

if( $obj->activeModule['carservicemaintenance']) {  
$rsSparePartType = $item->getSparePartType();
$arrItemPosition = $itemPosition->generateComboboxOpt(null, array('criteria' => ' and '. $itemPosition->tableName.'.statuskey = 1'));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript">
  
	jQuery(document).ready(function(){  
         
        var tabID = <?php echo ($isQuickAdd) ? $_GET['tabID'] : 'selectedTab.newPanel[0].id'; ?>

        var carCategory = new CarCategory(tabID, <?php echo json_encode(
            array(
                'rs' => $rs
            )
        ); ?>);

        prepareHandler(carCategory);

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
            },

            orderList: {
                validators: {
                    regexp: {
                        regexp: /^[0-9]+$/,
                        message: phpErrorMsg.orderList[2]
                    }
                }
            },
        };
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);

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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                     </div>  
                            </div>   
                    </div> 

                    <div class="div-table-col">
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                                <div class="form-group">
                                    <div class="col-xs-12">  
                                        <?php echo $obj->inputTextArea('trShortDesc', array('etc' => 'style="height:10em;"')); ?>
                                    </div>
                                </div>
                        </div>
                    </div>
                

            </div>
        </div>   
        <?php if( $obj->activeModule['carservicemaintenance']) { ?>
        <?php 
            if(!empty($rsSparePartType)) {

            $rsSparePartTypeAccess = $obj->reindexDetailCollections($rsSparePartTypeAccess, 'spareparttypekey');
                
        ?>

            <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col">

                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo strtoupper($obj->lang['partsPosition']); ?></div>

                            <?php 
                                
                            for ($i = 0; $i < count($rsSparePartType); $i++) { 

                                if($rsSparePartType[$i]['isposition'] != 1) {
                                    continue;
                                }
                                
                                $arrItemPositionList = $obj->getItemPosition($carcategorykey, $rsSparePartType[$i]['pkey']);
                                $arrDetailKeys = array_column($arrItemPositionList,'pkey');
                                $arrItemPositionList = array_column($arrItemPositionList,'itempositionkey');  

                            ?> 
                                <div style="margin-top:0.5em"><?php echo ucwords($rsSparePartType[$i]['name']); ?></div>  
                                <div class="item-position-access-list" style="">
                                    <?php echo $obj->inputHidden('hidDetailItemPositionKey_'.$rsSparePartType[$i]['pkey'].'[]', array('readonly'=>true,'value'=>implode(',', $arrDetailKeys) )); ?>
                                    <?php echo $obj->inputHidden('hidSparepartTypeKey[]', array('readonly' => true, 'value' => $rsSparePartType[$i]['pkey'])); ?>
                                    <?php echo $obj->inputSelect('selItemPosition_'.$rsSparePartType[$i]['pkey'].'[]', $arrItemPosition, array('value' => $arrItemPositionList,'etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                                </div> 
                            
                                
                            <?php  } ?>

                        </div>


                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo strtoupper($obj->lang['maintenanceReminder']); ?></div>

                            <?php 
                                
                                for ($i = 0; $i < count($rsSparePartType); $i++) { 

                                    if($rsSparePartType[$i]['isreminder'] != 1) {
                                        continue;
                                    }

                                    $rsItemInterval = $obj->getSparePartIntervalDetail($carcategorykey, $rsSparePartType[$i]['pkey']);
                                    if (!empty($rsItemInterval)) {
                                        $_POST['hidDetailSparePartIntervalKey_' . $rsSparePartType[$i]['pkey'] . '[]'] = $rsItemInterval[0]['pkey'];
                                        $_POST['month_' . $rsSparePartType[$i]['pkey'] . '[]'] = $obj->formatNumber($rsItemInterval[0]['month']);
                                        $_POST['mileage_' . $rsSparePartType[$i]['pkey'] . '[]'] = $obj->formatNumber($rsItemInterval[0]['mileage']);
                                    }

                            ?>


                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($rsSparePartType[$i]['name']); ?></label>  
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume">
                                                <?php echo $obj->inputHidden('hidDetailSparePartIntervalKey_'.$rsSparePartType[$i]['pkey'].'[]', array('readonly' => true)); ?>
                                                <?php echo $obj->inputHidden('hidIntervalSparepartTypeKey[]', array('readonly' => true, 'value' => $rsSparePartType[$i]['pkey'])); ?>
                                                <?php echo $obj->inputNumber('mileage_' . $rsSparePartType[$i]['pkey'] . '[]'); ?>
                                            </div>
                                            <div class="text-muted"> / <?php echo ucwords($obj->lang['mileage']); ?></div>
                                            <div class="consume">
                                                <?php echo $obj->inputNumber('month_' . $rsSparePartType[$i]['pkey'] . '[]'); ?>
                                            </div>
                                            <div class="text-muted"> / <?php echo ucwords($obj->lang['month']); ?></div>
                                        </div>
                                    </div>
                                </div> 

                            <?php } ?>
                        </div>

                    </div>

                    <div class="div-table-col">

                        <div class="div-tab-panel"> 
                            
                        </div>

                    </div>
                </div>
            </div>

        <?php } ?>
        <?php } ?>
 	 
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
