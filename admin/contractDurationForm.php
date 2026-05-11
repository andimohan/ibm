<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $contractDuration;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'contractDurationList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    
	$_POST['name'] = $rs[0]['name'];
    $_POST['interest'] = $obj->formatNumber($rs[0]['interest'],2);
    $_POST['selInterestMaturity'] =  $rs[0]['interestmaturitykey'] ;
    $_POST['fine'] = $obj->formatNumber($rs[0]['fine']);
    $_POST['selFineMaturity'] =  $rs[0]['finematuritykey'] ;
	$_POST['duedays'] =  $obj->formatNumber($rs[0]['duedays']);  
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrInterestMaturity =  $class->convertForCombobox($obj->getInterestMaturity(),'pkey','name'); 

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
         
			 
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.top[1]
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
				duedays: {
					validators: { 
						greaterThan: {
							value: -1,
							inclusive: false,
							separator: ',', 
							message: phpErrorMsg.duedays[2]
						}
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
      
         <div class="div-table main-tab-table-1">
              <div class="div-table-row">
                    <div class="div-table-col">  
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
                                        <label class="col-xs-3 control-label">Nama Durasi</label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Jatuh Tempo (hari)</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('duedays'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Bunga (%)</label> 
                                        <div class="col-xs-4" style="padding-right:0;"> 
                                            <?php echo $obj->inputDecimal('interest'); ?>
                                        </div> 
                                        <div class="col-xs-1" style="padding: 0; text-align:center;  font-size: 1.2em; line-height: 2.5em">/</div> 
                                        <div class="col-xs-4" style="padding-left:0"> 
                                            <?php echo $obj->inputSelect('selInterestMaturity', $arrInterestMaturity); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Denda (IDR)</label> 
                                        <div class="col-xs-4" style="padding-right:0;"> 
                                            <?php echo $obj->inputNumber('fine'); ?>
                                        </div> 
                                        <div class="col-xs-1" style="padding: 0; text-align:center;  font-size: 1.2em; line-height: 2.5em">/</div> 
                                        <div class="col-xs-4" style="padding-left:0"> 
                                            <?php echo $obj->inputSelect('selFineMaturity', $arrInterestMaturity); ?>
                                        </div> 
                                     </div>
                            </div>   
                  </div>
             </div>
        </div>     
           
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>