<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CostRate.class.php'); 
$costRate = createObjAndAddToCol(new CostRate());
$truckingService = createObjAndAddToCol(new Service());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$warehouse = createObjAndAddToCol(new Warehouse());
$city = createObjAndAddToCol(new City());
$consignee = createObjAndAddToCol(new Consignee());
$location = createObjAndAddToCol(new Location());

$obj= $costRate;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'costRateList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array(); 
$editWarehouseInactiveCriteria = '';
$arrService = $truckingService->searchData('', '', true, ' and '.$truckingService->tableName.'.statuskey = 1 order by '.$truckingService->tableName.'.name asc');

$rsCost = $truckingCost->searchData($truckingCost->tableName.'.statuskey',1, true, ' and showincostrate = 1 and chargetype = 2','order by fixedcost desc, name asc');  
$rsCost = array_merge($costRate->rsDriverCommission, $rsCost);

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
       
    $_POST['name'] = $rs[0]['name'];  
    $_POST['hidCargoTypeKey'] = $rs[0]['cargotypekey']; 
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];      
    
	if (!empty($rs[0]['consigneekey'])){ 
        $_POST['hidConsigneeKey'] = $rs[0]['consigneekey'];     
        $rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
        $_POST['consigneeName'] = $rsConsignee[0]['name'];   
    }
    
    
	if (!empty($rs[0]['locationkey'])){
        $_POST['hidLocationKey'] = $rs[0]['locationkey'];    
        $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
        $_POST['locationName'] = $rsLocation[0]['name'];   
    }
    
	$_POST['trDesc'] = $rs[0]['trdesc'];   
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    
    
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
            
        var costRate = new CostRate(tabID); 
        prepareHandler(costRate);   
        
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
			
              
			   locationName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.location[1]
                        }
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>    
                                        <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                 
                                                    echo $obj->inputAutoComplete(array(  
                                                                        'element' => array('value' => 'consigneeName',
                                                                                           'key' => 'hidConsigneeKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-consignee.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        )  
                                                                      )
                                                                );  
                                            ?>  
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                    $popupOpt = (!$isQuickAdd) ? array(
                                                                        'url' => 'locationForm.php',
                                                                        'element' => array('value' => 'locationName',
                                                                               'key' => 'hidLocationKey'),
                                                                        'width' => '600px',
                                                                        'title' => ucwords($obj->lang['add'] . ' - ' .  $obj->lang['location'])
                                                                    )  : '';

                                                    echo $obj->inputAutoComplete(array(
                                                                        'objRefer' => $city,
                                                                        'revalidateField' => true, 
                                                                        'element' => array('value' => 'locationName',
                                                                                           'key' => 'hidLocationKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-location.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) ,
                                                                        'popupForm' => $popupOpt
                                                                      )
                                                                );  
                                            ?>  
                                        </div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cargoType']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputSelect('hidCargoTypeKey', $arrCargoType); ?> 
                                        </div> 
                                    </div>  
                             </div>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>  
                                </div> 
                            </div>   
                        </div>
                               
                    </div>
           </div>
      </div> 
      
      <!-- table detail -->
      <div class="div-table transaction-detail table-scroll" style="width:100%;">
        <div class="div-table-row"> 
            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['jobType']); ?></div>  
            <?php
                $totalCols = count($arrService); 
                for($a=0; $a<$totalCols; $a++){
                ?>
                <div class="div-table-col detail-col-header" style="width:0; text-align:right">
                    <?php echo $obj->inputHidden('hidItemKey[]',array('value' => $arrService[$a]['pkey'])); ?>
                </div>  
            <?php
                }
            ?>
                
            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div> 
             
        </div>
      </div>
      
      <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; padding:0.2em 0 0.5em 0;">
            <?php 
                $totalRows = count($rsDetail);
            
                $costColWidth= 100;
                $costRowHeaderWidth= 200; 
                $totalTableWidth = ($totalCols * $costColWidth) + $costRowHeaderWidth; 
             
                
                for ($i=0;$i<=$totalRows; $i++){  
                         
                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $etc = '';
                    if ($i == $totalRows){
                        $class = 'detail-row-template';
                        $classCost = 'cost-row-template';
                        $overwrite = false;
                        $etc = 'disabled="disabled"';
                    } else {
                        $_POST['hidJobTypeKey[]'] =  $rsDetail[$i]['jobtypekey']; 
                        $_POST['jobTypeName[]'] =  $rsDetail[$i]['jobtypename'];    
                    }
            ?>
            
                  
            <!-- detail row -->
            <div class="div-table-row  odd-style-adjustment odd-white <?php echo $class; ?> ">
                <div class="div-table-col detail-col-detail"  style="padding:0.2em 0 0.5em 0;"> 
                    <div class="scroll-panel" style="overflow:scroll; padding-bottom:1em"> 
                    <!-- jobtype row -->
                    <div class="div-table" style="width:<?php echo $totalTableWidth; ?>px">
                        <div class="div-table-row">
                            <div class="div-table-col detail-col-detail" style="position:sticky; left:0; width: <?php echo $costRowHeaderWidth;?>px" >
                                <?php  
                                    echo $obj->inputText('jobTypeName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                                    echo $obj->inputHidden('hidJobTypeKey[]',array('overwritePost' => $overwrite, 'etc' => $etc));   
                                ?>
                            </div>

                            <?php  for($k=0; $k<$totalCols; $k++){  ?>
                              <div class="div-table-col detail-col-detail"  style="width:<?php echo $costColWidth;?>px; text-align:right; font-weight: bold;">
                                    <?php echo ucwords($arrService[$k]['name']); ?> 
                                </div>  
                            <?php  } ?> 
                        </div>
                    </div>
                    <!-- end of service row --> 
                    <div class="div-table table-scrollable" style="width:<?php echo $totalTableWidth; ?>px">
                       <?php  for($j=0;$j<count($rsCost);$j++){ ?>
                        
                        <div class="div-table-row">
                            <div class="div-table-col detail-col-detail" style="position:sticky; left:0; width: <?php echo $costRowHeaderWidth;?>px" >
                                <?php  
                                    $_POST['costName[]'] = $rsCost[$j]['name'];  
                                    $_POST['hidCostKey[]'] = $rsCost[$j]['pkey'];
                                     
                                    if ($rsCost[$j]['fixedcost']) 
                                     $_POST['costName[]'] .= ' *';
                                     
                                    echo $obj->inputText('costName[]',array('readonly' => true)); 
                                    echo $obj->inputHidden('hidCostKey[]');   
                                ?>
                            </div>

                            <?php   
                                $arrCost = array();
                                if (!empty($rsDetail[$i]['jobtypekey'])) {    
                                    $rsJobCost = $obj->getJobCost($id, $rsDetail[$i]['jobtypekey'], $rsCost[$j]['pkey']);  
                                    for($ctr=0;$ctr<count($rsJobCost);$ctr++)
                                        $arrCost[$rsJobCost[$ctr]['itemkey']] = $rsJobCost[$ctr]['price'];
                                }
                                    
                                for($k=0; $k<$totalCols; $k++){ 
                                    $_POST['cost_'.$rsCost[$j]['pkey'].'_'.$arrService[$k]["pkey"].'[]'] =  (empty($arrCost[$arrService[$k]["pkey"]])) ? 0 : $obj->formatNumber($arrCost[$arrService[$k]["pkey"]]);
                            ?>
                                <div class="div-table-col detail-col-detail" style="width:<?php echo $costColWidth;?>px; text-align:right">
                                    <?php  echo $obj->inputNumber('cost_'.$rsCost[$j]['pkey'].'_'.$arrService[$k]["pkey"].'[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc));    ?>
                                </div>
                            <?php } ?>   
                        </div>
                        
                       <?php } ?>
                    </div>
                    </div>
                    <!-- end of job row --> 
                </div>   
                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>" style="width:35px; vertical-align:top; text-align:center;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')); ?></div>
             </div> 
            <!-- end of detail row -->
          
           <?php } ?>
        </div> 
          
          <div class="asterix-label" style="clear:both; margin-top:1em;" ><span class="asterix">*</span> Harga <i>Fixed</i>.</div> 
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', ucwords($obj->lang['addRows']), array('class'=>'btn btn-primary btn-second-tone')); ?></div> 
        
        <div class="form-button-margin"></div> 
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();  ?>  
        </div> 
        
    </form>   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
