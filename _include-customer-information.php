<?php 
// khusus icommunity
 
if(!$security->isMemberLogin(false))  header('location:/logout'); 
if(!$class->isActiveModule('membershiplevel')) return ; // khusus icommunity sementaraa

includeClass(array('Customer.class.php' ,'MembershipLevel.class.php','CustomerFeatures.class.php'));
$customer = new Customer(); 
$customerFeatures = new CustomerFeatures();
$membershipLevel = new MembershipLevel();
 
$LOGIN_USER = $customer->searchDataRow(array($customer->tableName.'.pkey',
											 $customer->tableName.'.name',
											 $customer->tableName.'.membershiplevel',
											 $customer->tableName.'.hostlevelkey',
											 $customer->tableName.'.expdate',
											 $customer->tableName.'.email', 
											 $customer->tableName.'.emailprivacykey', 
											 $customer->tableName.'.mobileprivacykey', 
											 $customer->tableName.'.gmt'
											),
                                      ' and '.$customer->tableName.'.pkey = '.$customer->oDbCon->paramString(USERKEY)
                                      );

$LOGIN_USER = $LOGIN_USER[0];

$rsMembership = $membershipLevel->getDataRowById($LOGIN_USER['membershiplevel']);
$LOGIN_USER['membershiplevelname'] = $rsMembership[0]['name'];

$LOGIN_USER['expdate'] = (!$class->isEmptyDate($LOGIN_USER['expdate'])) ? $class->convertToLocalTimeZone($LOGIN_USER['expdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] ) : '0000-00-00';

$rsStat = $customer->getGamificationStat(USERKEY); 
$rsCustomerAchievement = $customerFeatures->getAchievementsDetail(USERKEY);

$arrTwigVar ['LOGIN_USER'] = array_merge($arrTwigVar ['LOGIN_USER'],$LOGIN_USER) ; 
$arrTwigVar ['rsStat'] =  $rsStat; 
$arrTwigVar ['achievement'] = $rsCustomerAchievement[USERKEY];
$arrTwigVar ['achievementLevel'] = $customerFeatures->getAchievementsLevel($rsCustomerAchievement[USERKEY]);

?>