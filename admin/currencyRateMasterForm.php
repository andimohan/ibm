<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CurrencyRateMaster.class.php');
$currencyRateMaster = createObjAndAddToCol(new CurrencyRateMaster()); 
$currency = createObjAndAddToCol(new Currency()); 


$obj = $currencyRateMaster;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;   

$formAction = 'currencyRateMasterList';
$rs = prepareOnLoadData($obj); 

$rsDetail  = array();

if (!empty($_GET['id'])){  
	$rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']); 
    
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate']);
    $_POST['bankName'] = $rs[0]['bankname'];

}else {
        $rsDetail = $currency->searchData($currency->tableName.'.statuskey',1,true, ' and ' .$currency->tableName.'.systemVariable <> 1'); 
        for($i=0;$i<count($rsDetail);$i++){ 

            $rsDetail[$i]['currencyname'] = $rsDetail[$i]['name'];
            $rsDetail[$i]['currencykey'] = $rsDetail[$i]['pkey'];
            $rsDetail[$i]['pkey'] = '';
            $rsDetail[$i]['rate'] = 0;     
        }

    $_POST['trDate'] = date(' d / m / Y');
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$rsCurrency = $currency->searchData ('statuskey','1',true,'and systemVariable = 0 order by name asc');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
		var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
   
        var varConstant = {  
            TABLEKEY : tablekey,
         };
 
		var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var currencyRate  = new CurrencyRate(tabID,varConstant);
    
        prepareHandler(currencyRate);    
			   
        var fieldValidation =  {
                                    code: {
										validators: {
											notEmpty: {
												message: phpErrorMsg.code[1]
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
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputAutoCode('code'); ?>
                            </div> 
                        </div>     
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trDate'); ?> 
                            </div> 
                        </div> 
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bank']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('bankName'); ?> 
                            </div> 
                        </div> 
                    
                        
                    </div>
                </div>
                
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-purple"><?php echo $obj->lang['currencyRate']; ?></div>
                             <div class="div-table  mnv-transaction transaction-detail" style="width:100%"> 
                                <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['currency']); ?></div> 
                                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;border:0"><?php echo ucwords($obj->lang['currentRate']); ?></div>  
    
                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:25px;border:0;"></div>   
                                </div> 
                                  <?php 
                            
                                        $totalRows = count($rsDetail);
                                        for ($i=0;$i<=$totalRows; $i++){  

                                            $class =  'transaction-detail-row';
                                            $overwrite = true;
                                            $etc = '';

                                            if ($i == $totalRows ){
                                                $class = 'detail-row-template';
                                                $overwrite = false;
                                                $etc = 'disabled="disabled"'; 
                                            } else {   

                                                $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                                                $_POST['hidCurrencyKey[]'] =  $rsDetail[$i]['currencykey']; 
                                                $_POST['currencyName[]'] =  $rsDetail[$i]['currencyname']; 
                                                $_POST['rate[]'] =   $obj->formatNumber($rsDetail[$i]['rate']);
                                            }
                                    ?>

                                 <div class="div-table-row <?php echo $class; ?>"> 
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('currencyName[]',array('overwritePost' => $overwrite, 'readonly' =>true,'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidCurrencyKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                     </div> 
                                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('rate[]',array('overwritePost' => $overwrite, 'etc' => ' style="text-align:right;"'. $etc)); ?></div> 
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="cashOut.calculateTotal()"')); ?></div>
                                </div>

                                <?php  } ?>  
                            </div>  

                            <div style="clear:both; height:1em;"></div>  
                    
                    </div>
                </div>
                
            </div>
        </div>       
                   
 	   <div style="clear:both"></div>
       
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>
   <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
