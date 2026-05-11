<?php  
require_once '../_config.php';  
require_once '../_include-v2.php'; 

includeClass(array('JobPosition.class.php','MembershipLevel.class.php','CampaignNewsletter.class.php','City.class.php','Country.class.php'));

$obj = createObjAndAddToCol( new CampaignNewsletter()); 
$country = createObjAndAddToCol(new Country());
$businessCategory = createObjAndAddToCol(new BusinessCategory());
$city = createObjAndAddToCol( new City());
$jobPosition =  createObjAndAddToCol( new JobPosition());
$membershipLevel =  createObjAndAddToCol( new MembershipLevel());
$lang = createObjAndAddToCol( new Lang());

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

$rsDetail = array();

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'campaignNewsletterList';
    
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rs = prepareOnLoadData($obj);  

$arrJobPosition = $jobPosition->generateComboboxOpt(null,array('criteria' =>' and ('.$jobPosition->tableName.'.statuskey = 1)', 'order' =>  'order by '.$jobPosition->tableName.'.name asc')); 
$arrMembership = $membershipLevel->generateComboboxOpt(null,array('criteria' =>  ' and ('.$membershipLevel->tableName.'.statuskey = 1)'));
$arrCity = $city->generateComboboxOpt(null,array('criteria' =>  ' and ('.$city->tableName.'.statuskey = 1)'));
$arrCountry = $country->generateComboboxOpt(null,array('criteria' =>  ' and ('.$country->tableName.'.statuskey = 1)'));
$arrNationality = $class->generateComboboxOpt(array('data' => $country->getNationality(), 'label' => 'nationality', 'value' => 'pkey'));
$arrLang = $lang->generateComboboxOpt(null,array('criteria' =>' and ('.$lang->tableName.'.statuskey = 1)', 'order' =>  'order by '.$lang->tableName.'.orderlist asc')); 


$arrBusinessCategory = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								   	' and '.$businessCategory->tableName.'.statuskey = 1' 
								  );
 
$arrBusinessCategory = $obj->generateComboboxOpt(array('data' => $arrBusinessCategory));  
  
$arrSex = $obj->generateComboboxOpt(array('data' => $obj->getSex())); 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  


$_POST['trDate'] = date('d / m / Y H:i'); 

if (!empty($_GET['id'])){   
    $id = $_GET['id'];
 
//    $rsDetail= $obj->getDetailWithRelatedInformation($id);
     
    
    $_POST['selSexKey[]'] = json_decode($rs[0]['sexkey']);
    $_POST['selMembershipKey[]'] = json_decode($rs[0]['membershipkey']);
    $_POST['selJobPositionKey[]'] = json_decode($rs[0]['jobpositionkey']);
    $_POST['selBusinessKey[]'] = json_decode($rs[0]['businesskey']);
    $_POST['selCityKey[]'] = json_decode($rs[0]['citykey']);
    $_POST['selCountryKey[]'] = json_decode($rs[0]['countrykey']);
    $_POST['selNationalityKey[]'] = json_decode($rs[0]['nationalitykey']);
    $_POST['selLangKey[]'] = json_decode($rs[0]['langkey']);
    
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i');
    
} 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
   <script type="text/javascript">
        jQuery(document).ready(function() {

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
            var opt = Array();

            var campaignNewsletter = new CampaignNewsletter(tabID, opt);

            prepareHandler(campaignNewsletter);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                        notEmpty: {
                            message: phpErrorMsg.name[1]
                        },
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
        <?php echo $obj->generateLangOptions(); ?>  
        
         <div class="div-table main-tab-table-2">
            <div class="div-table-row">
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']) ?></label> 
                            <div class="col-xs-9"> 
                                   <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                            </div> 
                        </div>  
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['code']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputAutoCode('code'); ?>
                            </div> 
                        </div> 
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['name']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputText('name'); ?>
                            </div> 
                        </div> 
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['publishDate']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputDateTime('trDate'); ?>
                            </div> 
                        </div> 
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['membership']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputSelect('selMembershipKey[]',$arrMembership, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div>
                        </div> 
                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['jobPosition']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputSelect('selJobPositionKey[]',$arrJobPosition, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div>
                        </div> 
                        
                        
<!--
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['age']); ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex">
                                       <div class="consume"><?php echo $obj->inputNumber('ageFrom'); ?></div>
                                       <div>-</div>
                                       <div class="consume"><?php echo $obj->inputNumber('ageTo'); ?></div>
                                </div>
                            </div>  
                        </div>  
-->
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputSelect('selSexKey[]',$arrSex, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputSelect('selCityKey[]',$arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['country']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputSelect('selCountryKey[]',$arrCountry, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['nationality']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputSelect('selNationalityKey[]',$arrNationality, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['language']); ?></label> 
                            <div class="col-xs-9">  
                                <?php echo $obj->inputSelect('selLangKey[]',$arrLang, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div> 
                        </div> 

                         <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  ucwords($obj->lang['businessCategory']) ?></label> 
                            <div class="col-xs-9">  
                                    <?php echo $obj->inputSelect('selBusinessKey[]',$arrBusinessCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); ?>
                            </div>
                        </div> 
                         
                    </div> 
                </div>
                  <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['content']); ?></div> 
                               <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?>                       
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
