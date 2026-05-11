<?php 

include '../_config.php'; 
include '../_include.php';  

$obj= $itemCondition;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'itemConditionList';  
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rsConditionDetail = array();
$rs = prepareOnLoadData($obj); 
$arrCondition = array();
if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	 
    $rsConditionDetail = $obj->getDetailById($id);  
    $rsConditionDetail = array_column($rsConditionDetail, null, 'marketplacekey'); 
     
    $_POST['name'] = $rs[0]['name'];  
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
//$rsMarketplace = $marketplace->searchData('','',true,' and '.$marketplace->tableName.'.statuskey = 1');
$marketplaceObjs = $marketplace->getMarketplaceObj();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
    
   jQuery(document).ready(function(){  
	 	 
       var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID); 
			 
		 $('#defaultForm-' + tabID)
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
            }
        })
        .on('success.form.bv', function(e) {
              <?php echo $obj->submitFormScript(); ?>
        });
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
                    <div class="div-table-col" >  
                  		   	<div class="div-tab-panel">      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Status</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div>     
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Kode</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Nama</label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                     </div> 
<!--
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Note</label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                     </div>  
-->
                            </div>   
                  </div> 
                    <?php if (!$isQuickAdd){ ?> 
                    <div class="div-table-col">	
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-purple"><?php echo $obj->lang['marketplace']; ?></div>
                        <div class="div-table transaction-detail  no-odd-even-style" style="width:100%"> 

                              <?php  
                                  $totalRows = count($marketplaceObjs); 

                                  for ($i=0;$i<$totalRows; $i++){  
                                        $marketplaceObj =  $marketplaceObjs[$i]['obj'];
                                        $arrCondition = (isset($marketplaceObj->itemCondition)) ? $marketplaceObj->itemCondition : array();
                                      
                                        $class =  'transaction-detail-row marketplace-condition-row';
                                        $style = '';
                                        $overwrite = true; 
                                        $disabled = false;

                                        if ($i == $totalRows ){
                                            $class = 'condition-row-template';
                                            $style = 'style="display:none"';
                                            $overwrite = false;
                                            $disabled = true;  
                                        } else {    
                                            $marketplacekey = $marketplaceObjs[$i]['key']; 
                                            $_POST['hidMarketplaceKey[]'] = $marketplacekey;  

                                            $_POST['hidDetailKey[]'] = '';
                                            $_POST['marketplaceConditionName[]'] = '';
                                            $_POST['hidMarketplaceConditionKey[]'] = '';

                                            if (isset($rsConditionDetail[$marketplacekey])){ 
                                                $_POST['hidDetailKey[]'] = $rsConditionDetail[$marketplacekey]['pkey']; 
                                                $_POST['selCondition[]'] = $rsConditionDetail[$marketplacekey]['marketplaceconditionkey'];    
                                            }
                                        }


                                ?>

                                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                                    <div class="div-table-col detail-col-detail" style="width: 150px">
                                        <?php echo $marketplaceObjs[$i]['name']; ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidMarketplaceKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputSelect('selCondition[]',$arrCondition ,array('overwritePost' => $overwrite,'disabled' => $disabled )); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div> 

                                <?php } ?>
                        </div>  
                        <div style="clear:both; height:1em;"></div>   
                      </div> 
                    </div>
                    <?php } ?> 
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
