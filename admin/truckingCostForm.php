<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Service.class.php','VatOut.class.php'));
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount());  
$serviceCategory = createObjAndAddToCol( new ServiceCategory());  
$truckingServiceOrderCategory = createObjAndAddToCol( new TruckingServiceOrderCategory()); 
$vatOut = createObjAndAddToCol(new VatOut()); 
    
$obj= $truckingCost; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'truckingCostList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$truckingCostCOAType = $obj->loadSetting('truckingCostCOAType');

$rsItemDescription = array();  
$rsCostCOAKey = array();

$rs = prepareOnLoadData($obj); 
 
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
 	
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage']);
    
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $serviceCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $serviceCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    $_POST['chkIsDropPointDetailPrice'] = $rs[0]['isdroppointdetailprice'];
    $_POST['chkIsMultipliedByQty'] = $rs[0]['ismultipliedbyqty'];

    
    $rsServiceCode = $vatOut->getTaxServiceCode(' and pkey = ' . $obj->oDbCon->paramString($rs[0]['taxservicecodekey']));
    $_POST['taxServiceCode'] = $rsServiceCode[0]['code'];    
    
    $rsServiceUnit = $vatOut->getTaxServiceUnit(' and pkey = ' . $obj->oDbCon->paramString($rs[0]['taxserviceunitkey']));
    $_POST['taxServiceUnit'] = $rsServiceUnit[0]['code'];    
    
    if($truckingCostCOAType == 2) { 
        $rsCostCOAKey = $obj->getCostCOADetail($id); 
    }else{
        $_POST['hidCostCOAKey'] = $rs[0]['costcoakey']; 
        if (!empty($rs[0]['costcoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['costcoakey']);
            $_POST['costCOALink'] = $rsCoa[0]['code'] . ' - ' .$rsCoa[0]['name'];
        }

        $_POST['hidRevenueCOAKey'] = $rs[0]['revenuecoakey']; 
        if (!empty($rs[0]['revenuecoakey'])){
            $rsRevenue = $chartOfAccount->getDataRowById($rs[0]['revenuecoakey']);
            $_POST['revenueCOALink'] = $rsRevenue[0]['code'] . ' - ' .$rsRevenue[0]['name'];
        }
    }

} else{
    /*$rsCOA = $coaLink->getCOALink('defaulttruckingcost');
    $_POST['hidCostCOAKey'] = $rsCOA[0]['coakey'];
    $_POST['costCOALink'] = $rsCOA[0]['value'];
        
    $rsCOA = $coaLink->getCOALink ('defaulttruckingrevenue');
    $_POST['hidRevenueCOAKey'] = $rsCOA[0]['coakey'];
    $_POST['revenueCOALink'] = $rsCOA[0]['value'];*/
    
    $_POST['chkShowInTrucking'] = 1;
    $_POST['chkShowInCostRate'] = 1;
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
 
<style>
.item-filter  {list-style:none; padding:0; margin:0}
.item-filter li{float:left; margin: 0.2em 0.2em 0em 0.2em; display:inline-block; }    
.item-filter li label{width:150px; cursor:pointer;background-color:#dedede; padding:0.7em 1em 1em 1em;}
.item-filter li input {font-size:1.5em;margin-right:0.2em; }
</style>  

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript">   
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);
           
		// BOOTSTRAP VALIDATOR
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
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
				
				categoryName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.category[1]
                        },  
                    }
                },  
                
                <?php if ($truckingCostCOAType == 2){} else {?>
                    costCOALink: { 
                        validators: {
                            notEmpty: {
                                message: phpErrorMsg.coa[1]
                            },  
                        }
                    },  

                    revenueCOALink: { 
                        validators: {
                            notEmpty: {
                                message: phpErrorMsg.coa[1]
                            },  
                        }
                    },   
        
                <?php }?>
				
                 
				
            }
        })
        .on('success.form.bv', function(e) {   
                 <?php echo $obj->submitFormScript(); ?>
        }); 
         
        <?php if (!isset($rsItemDescription) || empty($rsItemDescription)) {  ?> 
            addNewTemplateRow("item-description-row-template");  
        <?php } ?>
        
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['costName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('aliasName'); ?>
                                        </div> 
                                    </div>       
                                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $serviceCategory,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-service-category.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'serviceCategoryForm.php',
                                                                                                            'element' => array('value' => 'categoryName',
                                                                                                                   'key' => 'hidCategoryKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['serviceCategory'])
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                            </div> 
                                        </div>
                                    
                                <!--<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sellingPrice']); ?></label> 
                                        <div class="col-xs-9"> 
                                                 <?php echo $obj->inputNumber('sellingPrice'); ?>
                                        </div> 
                                    </div> 
                                -->
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('shortdescription',array('etc'=>'style="height:8em;"')); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                         <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['needDocumentProof']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputCheckBox('chkIsDocument'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group" style="margin-bottom:0">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['showIn']); ?></label> 
                                        <div class="col-xs-1" > 
                                             <?php echo $obj->inputCheckBox('chkShowInTrucking'); ?>  
                                        </div> 
                                        <div class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['trucking']); ?>
                                        </div> 
                                        <div class="col-xs-1" > 
                                             <?php echo $obj->inputCheckBox('chkShowInCostRate'); ?>  
                                        </div> 
                                        <div class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['costRate']); ?>
                                        </div>  
                                    </div> 
                       
                                       
                                    <!--<div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-1" > 
                                             <?php echo $obj->inputCheckBox('chkShowInDepot'); ?>  
                                        </div> 
                                        <div class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['depot']); ?>
                                        </div> 
                                        <div class="col-xs-1" > 
                                             <?php echo $obj->inputCheckBox('chkShowInTerminal'); ?>  
                                        </div> 
                                        <div class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['terminal']); ?>
                                        </div> 
                                        <div class="col-xs-1" > 
                                             <?php echo $obj->inputCheckBox('chkShowInShippingCompany'); ?>  
                                        </div> 
                                        <div class="col-xs-2 control-label" style="padding-left:0"> 
                                             <?php echo ucwords($obj->lang['shippingCompany']); ?>
                                        </div> 
                                    </div>-->
                       
                                       
                           </div>     
                    </div> 
                  <div class="div-table-col">  
                        <div class="div-tab-panel"> 
                        <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['finance']); ?></div>
                      
                        <?php if (USE_GL && ( empty($rs[0]['pkey'])  || $security->isAdminLogin('ChartOfAccount',10) ) ) {   ?>
                            
                            <?php if ($truckingCostCOAType == 2){
                                    // kalo dipecah per kategori (seperti logol)
                                    $rsCategory = $truckingServiceOrderCategory->searchDataRow( array('pkey','name'), ' and '.$truckingServiceOrderCategory->tableName.'.statuskey = 1');
                                 
                                    $arrCostType = array(
                                        '1' => $obj->lang['expenseAccount'],
                                        '2' => $obj->lang['revenueAccount'], 
                                    );
                                              
                                    $costCOAByType = array();
                                    $arrTempCOA = array(); 
    
                                    if(!empty($rsCostCOAKey)){
                                        $costCOAByType = $obj->reindexDetailCollections($rsCostCOAKey,'typekey'); 
                                        $arrTempCOA = $chartOfAccount->searchDataRow(array('pkey','code','name'),' and '.$chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString(array_column($rsCostCOAKey,'coakey'),',').') ');
                                        $arrTempCOA = array_column($arrTempCOA,null,'pkey');
                                    }
    
                                    foreach($arrCostType as $typekey => $typeRow){
                                        $arrCostCOAKey = array();
                                        if(!empty($rsCostCOAKey)){  
                                            $arrCostCOAKey = $costCOAByType[$typekey];
                                            $arrCostCOAKey = array_column($arrCostCOAKey,null,'categorykey');
                                        }
                                        
                                        echo '<div class="col-xs-12 section-title">'.ucwords($typeRow).'</div>'; 
                                        foreach($rsCategory as $categoryRow){ 
                                            $categorykey = $categoryRow['pkey'];
                                            if(!empty($arrCostCOAKey[$categorykey])){ 
                                                $_POST['hidCostCOADetailKey[]'] = $arrCostCOAKey[$categorykey]['pkey']; 
                                                $_POST['hidCostCOAKeyDetail[]'] = $arrCostCOAKey[$categorykey]['coakey'];
                                                $_POST['costCOALinkDetail[]'] = (!empty($arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']])) ? $arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']]['code'] .' - '. $arrTempCOA[$arrCostCOAKey[$categorykey]['coakey']]['name'] : '' ;
                                            }else{
                                                $_POST['hidCostCOADetailKey[]']  = '';
                                                $_POST['hidCostCOAKeyDetail[]']  = '';
                                                $_POST['costCOALinkDetail[]']  = '';
                                            }

                                            $hidDetailKey = $obj->inputHidden('hidCostCOADetailKey[]'); 
                                            $typeKeyInput = $obj->inputHidden('typeKeyCOA[]',array( 'value' => $typekey)); 
                                            $categoryKeyInput = $obj->inputHidden('categoryKeyCOA[]',array( 'value' => $categoryRow['pkey'])); 

                                            $coaLinkInput = $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'costCOALinkDetail[]', 'key' => 'hidCostCOAKeyDetail[]'),
                                                                                    'source' =>array(  'url' => 'ajax-coa.php', 'data' => array(  'action' =>'searchData' ) )  
                                                                                    )); 

                                            echo '     
                                            <div class="form-group">
                                                <label class="col-xs-3 control-label">'.$hidDetailKey.$typeKeyInput.$categoryKeyInput.$categoryRow['name'].'</label> 
                                                <div class="col-xs-9">'.$coaLinkInput.'</div> 
                                            </div>';

                                        }        
                                    } 
                                        
                                    echo  '<div style="clear:both; height: 2em"></div>';
                                             
                                  }else{ // normal ?> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['expenseAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array( 
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'costCOALink',
                                                                                               'key' => 'hidCostCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueAccount']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php 
                                                       echo $obj->inputAutoComplete(array( 
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'revenueCOALink',
                                                                                               'key' => 'hidRevenueCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            )  
                                                                            )); 
                                             ?>    
                                        </div> 
                                    </div>
    
                           <?php  } ?>
                        <?php } ?>
                            
                            <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxServiceCode']); ?></label> 
                                            <div class="col-xs-9"> 
                                                    <?php 
                                                           echo $obj->inputAutoComplete(array( 
                                                                                'element' => array('value' => 'taxServiceCode',
                                                                                                   'key' => 'hidTaxServiceCodeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vat-out.php',
                                                                                                    'data' => array(  'action' =>'getTaxServiceCode' )
                                                                                                )  
                                                                                )); 
                                                 ?>    


                                            </div> 
				            </div>
                            <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxServiceUnit']); ?></label> 
                                            <div class="col-xs-9"> 
                                                    <?php 
                                                           echo $obj->inputAutoComplete(array( 
                                                                                'element' => array('value' => 'taxServiceUnit',
                                                                                                   'key' => 'hidTaxServiceUnitKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vat-out.php',
                                                                                                    'data' => array(  'action' =>'getTaxServiceUnit' )
                                                                                                )  
                                                                                )); 
                                                 ?>    


                                            </div> 
				            </div>
                            <div class="form-group" >

                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tax']); ?></label> 
                                <div class="col-xs-9" style="padding-top:5px">
                                    <div class="flex" >
                                        <div><?php echo $obj->inputCheckBox('chkIsTax23'); ?></div>
                                        <div ><?php echo ucwords($obj->lang['tax23']); ?></div>
                                        <?php if ($obj->loadSetting('usePPNDetail')) { ?> 
                                        <div  style="margin-left:5em"><?php echo ucwords($obj->lang['PPN']); ?></div>
                                        <div style="width:7em"><?php echo $obj->inputNumber('taxPercentage'); ?></div>
                                        <div>%</div>
                                        <div style="margin-left:1em"><?php echo $obj->inputCheckBox('chkIsPriceIncludeTax'); ?></div>
                                        <div><?php echo ucwords('Include'); ?></div>
                                        <?php } ?>
                                    </div>  
                                </div> 
                            </div>
                            
                            <div class="form-group" >
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['others']); ?></label> 
                                <div class="col-xs-9" style="padding-top:5px">
                                    <div class="flex"> 
                                        <div><?php echo $obj->inputCheckBox('chkIsReimburse'); ?></div>
                                        <div><?php echo ucwords($obj->lang['reimburse']); ?></div>
                                        <div style="margin-left:1em"><?php echo $obj->inputCheckBox('chkIsShareProfit'); ?> </div>
                                        <div><?php echo ucwords($obj->lang['shareProfit']); ?></div>
                                        <div style="margin-left:1em"><?php echo $obj->inputCheckBox('chkIsDropPointDetailPrice'); ?> </div>
                                        <div><?php echo ucwords($obj->lang['dropPointDetailPrice']); ?></div>
                                    </div> 
                                </div>    
                                <div class="col-xs-9" style="padding-top:5px">
                                    <div class="flex"> 
                                        <div><?php echo $obj->inputCheckBox('chkIsMultipliedByQty'); ?></div>
                                        <div><?php echo ucwords($obj->lang['multipliedByQty']); ?></div>
                                    </div> 
                                </div>  
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['costType']); ?></label> 
                                <div class="col-xs-9 control-label" style="padding-top:5px"> 
                                    <div class="flex"> 
                                        <div><?php echo $obj->inputCheckBox('chkIsFixedCost'); ?></div>
                                        <div><?php echo ucwords($obj->lang['fixedCost']); ?></div>
                                        <div style="margin-left:1em">
                                         <?php
                                                $options = array();
                                                array_push($options,array('label' => ucwords($obj->lang['perDocument']), 'value' => '1' ));
                                                array_push($options,array('label' => ucwords($obj->lang['perItem']), 'value' => '2' )); 
                                                echo $obj->inputRadio('rdbChargeType', array('optionItems' => $options)); 
                                            ?> 
                                        </div>
                                    </div>  
                                </div>  
                            </div> 


                      </div>
                  </div>
             </div>   
       </div>
             
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div>  
    </form>  
     <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
