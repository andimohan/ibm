<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('JournalBalancing.class.php');
$journalBalancing = createObjAndAddToCol(new JournalBalancing());
$coa = createObjAndAddToCol(new ChartOfAccount());


$obj= $journalBalancing;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'journalBalancingList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$rsDetail = array(); 

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])){ 
// Yang berupa string pake new load
 
    if( !empty($rs[0]['coatokey'])) { 
        $rsTemporaryCOA = $coa->getDataRowById($rs[0]['coatokey']);
        $_POST['temporaryCOAName'] = $rsTemporaryCOA[0]['name'];
    }
     
    if(!empty($rs[0]['coakey']) ) {
        $rsCOA = $coa->getDataRowById($rs[0]['coakey']);
        $_POST['coaName'] = $rsCOA[0]['name']; 
    }
        
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center;}
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
    .options-row .form-panel-result {max-height: 10em; overflow: auto}
</style>
<title></title> 

<script type="text/javascript">   
 
	jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;  
	 	 var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
         var itemIn = new ItemIn(tabID,tablekey);
    
         prepareHandler(itemIn);   
        
         var fieldValidation =  {code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                 }
                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
        
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
                               <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputAutoCode('code'); ?>
                            </div> 
                        </div>   
                        <!--
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo  $obj->inputText('refCode',array('readonly' => true)); ?>
                            </div> 
                        </div>   
                        -->
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputDate('trDate'); ?>  
                            </div> 
                        </div>    
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['account']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputAutoComplete(array(
                                            'objRefer' => $coa,
                                            'revalidateField' => false, 
                                            'element' => array('value' => 'coaName',
                                                               'key' => 'hidCOAKey'),
                                            'source' => array(
                                                                'url' => 'ajax-coa.php',
                                                                'data' => array('action' =>'searchData')
                                                            )
                                          )
                                );?>  
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['temporaryAccount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputAutoComplete(array(
                                            'objRefer' => $coa,
                                            'revalidateField' => false, 
                                            'element' => array('value' => 'temporaryCOAName',
                                                               'key' => 'hidCOAToKey'),
                                            'source' => array(
                                                                'url' => 'ajax-coa.php',
                                                                'data' => array('action' =>'searchData')
                                                            )
                                          )
                                );?>  
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputNumber('amount'); ?>
                            </div> 
                        </div> 
                    </div> 
                </div> 
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                            </div> 
                        </div>   
                    </div>
                </div>
             </div>
        </div>                 

          <div style="clear:both; height:1em;"></div> 
          
    
        <div class="form-button-margin"></div>
         <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton(array(), true);?>
        </div> 
        
    </form>  
     <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
