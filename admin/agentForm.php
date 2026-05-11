<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $agent;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasAPAccess = $security->isAdminLogin($ap->securityObject,10);  
    
$formAction = 'agentList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editTermOfPaymentInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  
$rsContactPerson = array();

if (!empty($_GET['id'])){ 
    
    $id = $_GET['id'];
    $rsContactPerson = $obj->getContactPerson($id);
    
    $_POST['name'] = $rs[0]['name']; 
	$_POST['address1'] = $rs[0]['address1']; 
	$_POST['address2'] = $rs[0]['address2']; 
	$_POST['zipCode'] = $rs[0]['zipcode']; 
	$_POST['phone'] = $rs[0]['phone']; 
	$_POST['mobile'] = $rs[0]['mobile']; 
	$_POST['email'] = $rs[0]['email']; 
	$_POST['fax'] = $rs[0]['fax']; 
	$_POST['description'] = $rs[0]['description']; 
	$_POST['accountBank'] = $rs[0]['accountbank']; 
	$_POST['accountNo'] = $rs[0]['accountno']; 
	$_POST['accountName'] = $rs[0]['accountname']; 
	$_POST['hidCityKey'] = $rs[0]['citykey']; 
	$_POST['selTermOfPayment'] = $rs[0]['termofpaymentkey']; 
	$_POST['taxid'] = $rs[0]['taxid']; 
	$_POST['taxRegistrationName'] = $rs[0]['taxregistrationname']; 
	$_POST['taxRegistrationAddress'] = $rs[0]['taxregistrationaddress']; 
	
    $apoustanding = ($hasAPAccess) ? $rs[0]['apoutstanding'] : 0; 
	$_POST['apoutstanding'] = $obj->formatNumber($apoustanding);   
    
        
	if (!empty($_POST['hidCityKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    $_POST['hidAPCOAKey'] = $rs[0]['apcoakey'];  
    if (!empty($rs[0]['apcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['apcoakey']); 
		$_POST['APCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
	}
		
    $_POST['hidDownpaymentCOAKey'] = $rs[0]['downpaymentcoakey'];  
    if (!empty($rs[0]['downpaymentcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['downpaymentcoakey']); 
		$_POST['downpaymentCOA'] = $rsCOA[0]['code'] . ' - ' . $rsCOA[0]['name'];
	}
    
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
}


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrTOP = $class->convertForCombobox($termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc'),'pkey','name'); 

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
                            message: phpErrorMsg.agent[1]
                        },  
                    }
                },   
				
				email: { 
                    validators: { 
                        emailAddress: {
							message:  phpErrorMsg.email['3']
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name');   ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('address1');   ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('address2');   ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php   
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'cityForm.php',
                                                                'element' => array('value' => 'cityName', 'valueDBField' => 'citycategoryname',
                                                                                    'key' => 'hidCityKey'),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
                                                            )  : '';
                                    
                                                        echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'element' => array('value' => 'cityName',
                                                                                   'key' => 'hidCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                ) ,
                                                                'popupForm' => $popupOpt
                                                              )
                                                        );  
                                            ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('zipCode'); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                        <div class="col-xs-4" style="padding-right:0"> 
                                            <?php echo $obj->inputText('phone'); ?>
                                        </div>  
                                        <div class="col-xs-5" style="padding-left:5px"> 
                                            <?php echo $obj->inputText('mobile'); ?>
                                        </div> 
                                    </div>      
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['fax']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('fax'); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('email'); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputTextArea('description',array('etc' => 'style="height:8em;"') );   ?> 
                                        </div> 
                                    </div>    
                                 
                                    
                           </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel"> 
                              <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['financialInformation']); ?></div>

                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['top']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selTermOfPayment', $arrTOP); ?> 
                                </div> 
                              </div>   
 
                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankName']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('accountBank');   ?> 
                                </div> 
                              </div>   
                            
                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('accountNo');   ?>  
                                </div> 
                              </div>   
  
                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountName']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('accountName');   ?>   
                                </div> 
                              </div>   
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('apoutstanding', array('etc' => 'readonly="readonly"') );   ?>    
                                    </div> 
                            </div>  
                              <div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"></div> 
                                    </div>  
                             <div class="form-group"  >
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxIdentificationNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('taxid');   ?>    
                                    </div> 
                            </div>   
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxRegistrationName']); ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('taxRegistrationName');   ?>    
                                    </div> 
                            </div>  
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxRegistrationAddress']); ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('taxRegistrationAddress');   ?>    
                                    </div> 
                            </div>  
                            <?php if (USE_GL){ ?>   
                              <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['apAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'APCOA',
                                                                                                       'key' => 'hidAPCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'APCOA',
                                                                                                                   'key' => 'hidAPCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                               </div> 
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['downpaymentAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $chartOfAccount,
                                                                                    'element' => array('value' => 'downpaymentCOA',
                                                                                                       'key' => 'hidDownpaymentCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) ,
                                                                                    'popupForm' => array(
                                                                                                            'url' => 'chartOfAccountForm.php',
                                                                                                            'element' => array('value' => 'downpaymentCOA',
                                                                                                                   'key' => 'hidDownpaymentCOAKey'),
                                                                                                            'width' => '600px',
                                                                                                            'title' => $obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount']
                                                                                                        )
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            <?php } ?>
                        </div>
                        
                        <?php if (!$isQuickAdd) { ?> 
                       <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['contactPerson']); ?></div>
                            <?php echo $obj->contactPersonPlugin($rsContactPerson); ?>     
                       </div>
                        <?php } ?>
                    </div>
            </div>
      </div>     
      <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>  
    </form>
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>