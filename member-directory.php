<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('Customer.class.php','MembershipSubscription.class.php','JobPosition.class.php','BusinessCategory.class.php','CustomerFeatures.class.php'));
$customer = new Customer();  
$membershipSubscription = new MembershipSubscription();
$jobPosition = new JobPosition();
$businessCategory = new BusinessCategory();
$city = new City();
$country = new Country();
$membershipLevel = new MembershipLevel();
$customerFeatures =  new CustomerFeatures();

$pageIndex =  ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$arrTwigVar ['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('productTotalItemPerPage');

$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

//$criteria  = ' AND ' . $customer->tableName . '.pkey <>' . $class->oDbCon->paramString(USERKEY);
$criteria = '';
$pageUrlParam = array();
$arrSearchCriteria = array();
$arrLabelFilter = array();

if(isset($_GET['search'])) $_POST['search'] = $_GET['search'];
if (isset($_POST) && !empty($_POST['search'])) { 
	$searchkey = $_POST['search'];
	array_push($arrSearchCriteria, ' ( 
											'.$customer->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $searchkey . '%') . ') OR
											'.$customer->tableName . '.name LIKE (' . $class->oDbCon->paramString('%' . $searchkey . '%') . ')
									)');  
	
	 array_push($pageUrlParam,"search=" . $searchkey);
	
}

$arrOpt = array(
			  array(
			  		'name' => $class->lang['membership'],	
			  		'param' => 'selMembership',	
			  		'dbfield' =>  $customer->tableName . '.membershiplevel',	
			  ),
			  array(
			  		'name' => $class->lang['sex'],	
			  		'param' => 'selSex',	
			  		'dbfield' =>  $customer->tableName . '.sexkey',	
			  ),
			  array(
			  		'name' => $class->lang['jobPosition'],	
			  		'param' => 'selJobPosition',	
			  		'dbfield' =>  $customer->tableName . '.jobpositionkey',	
			  ),
			  array(
				  	'name' => $class->lang['businessCategory'],
			  		'param' => 'selBusiness',	
			  		'dbfield' =>  $customer->tableName . '.mainbusinesskey',	
			  ),
			  array(
				  	'name' => $class->lang['city'],
			  		'param' => 'selCity',	
			  		'dbfield' =>  $customer->tableName . '.citykey',	
			  ),
              array(
				  	'name' => $class->lang['country'],
			  		'param' => 'selCountry',	
			  		'dbfield' =>  $customer->tableName . '.countrykey',	
			  ),
              array(
				  	'name' => $class->lang['nationality'],
			  		'param' => 'selNationality',	
			  		'dbfield' =>  $customer->tableName . '.nationalitykey',	
			  ),
		);


foreach($arrOpt as $row){
	
	if(isset($_GET[$row['param']])) $_POST[$row['param']] = explode(',',$_GET[$row['param']]);
	if(isset( $_POST[$row['param']] ) && !empty(  $_POST[$row['param']] )){
		$value = $_POST[$row['param']];
		if(!is_array($value)) $value = array($value);

		$_POST[$row['param'].'[]'] = $value;
		array_push($arrSearchCriteria, $row['dbfield']. ' in (' . $class->oDbCon->paramString($value,',') . ')'); 
		array_push($pageUrlParam,$row['param']."=" . implode(',',$value)); 
		
		
	 	array_push($arrLabelFilter, $row['name']);
	}
	 
} 


if(!empty($arrSearchCriteria))
	$criteria .= ' AND ' .implode(' AND ', $arrSearchCriteria);

$criteria .= ' and '.$customer->tableName.'.statuskey = 2';

$rs = $customer->searchData('','',true,$criteria,' order by '.$customer->tableName.'.modifiedon desc, '.$customer->tableName.'.createdon desc',$limit );

$arrCustomerKey = array_column($rs,'pkey');
$rsCustomerAchievement = $customerFeatures->getAchievementsDetail($arrCustomerKey);

for($i=0;$i<count($rs);$i++){ 
 
		$rs[$i]['achievementLevel'] =  $customerFeatures->getAchievementsLevel($rsCustomerAchievement[$rs[$i]['pkey']]);
 
		if($LOGIN_USER['membershiplevel'] < $rs[$i]['emailprivacykey'])  $rs[$i]['email'] = ''; 
		if($LOGIN_USER['membershiplevel'] < $rs[$i]['mobileprivacykey'])  $rs[$i]['mobile'] = ''; 
	
		$rs[$i]['prospectdescription'] = str_replace(chr(13),'<br>',$rs[$i]['prospectdescription']);
		$rs[$i]['offerdescription'] = str_replace(chr(13),'<br>',$rs[$i]['offerdescription']);
		$rs[$i]['photohash']= getPHPThumbHash($rs[$i]['photofile']);  

        $rs[$i]['citycountryname'] = (!empty($rs[$i]['cityandcategoryname'])) ? $rs[$i]['cityandcategoryname'].', '.$rs[$i]['countryname'] : $rs[$i]['countryname'];
}

$totalPages = ceil( $customer->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages; 
 
$rsCustomerLatest = $membershipSubscription->getLatestUpgradeCustomer(3);

for($i=0;$i<count($rsCustomerLatest);$i++){ 
		if($LOGIN_USER['membershiplevel'] < $rsCustomerLatest[$i]['emailprivacykey'])  $rsCustomerLatest[$i]['email'] = ''; 
		if($LOGIN_USER['membershiplevel'] < $rsCustomerLatest[$i]['mobileprivacykey'])  $rsCustomerLatest[$i]['mobile'] = ''; 
	
		$rsCustomerLatest[$i]['prospectdescription'] = str_replace(chr(13),'<br>',$rsCustomerLatest[$i]['prospectdescription']);
		$rsCustomerLatest[$i]['offerdescription'] = str_replace(chr(13),'<br>',$rsCustomerLatest[$i]['offerdescription']);
		$rsCustomerLatest[$i]['photohash']= getPHPThumbHash($rsCustomerLatest[$i]['photofile']);  
}
 
$arrSex = $class->generateComboboxOpt(array('data' => $class->getSex()));
$arrJobPosition = $jobPosition->generateComboboxOpt(null,array('criteria' =>' and ('.$jobPosition->tableName.'.statuskey = 1)', 'order' =>  'order by '.$jobPosition->tableName.'.name asc')); 

$arrBusiness = $businessCategory->searchDataRow(array($businessCategory->tableName.'.pkey',$businessCategory->tableName.'.name'),
								' and '.$businessCategory->tableName.'.statuskey = 1 '
							  );
$arrBusiness = $class->generateComboboxOpt(array('data' => $arrBusiness));

$arrCity = $city->searchData( $city->tableName.'.statuskey', 1);
$arrCity = $class->generateComboboxOpt(array('data' => $arrCity, 'label' => 'citycategoryname'));


$arrCountry =  $country->generateComboboxOpt(null,array('criteria' =>' and ('.$country->tableName.'.statuskey = 1)')); 
$arrNationality = $class->generateComboboxOpt(array('data' => $country->getNationality(), 'label' => 'nationality', 'value' => 'pkey'));
$arrMembership = $membershipLevel->generateComboboxOpt(null,array('criteria' =>  ' and ('.$membershipLevel->tableName.'.statuskey = 1)'));
	
$arrTwigVar ['rsCustomerLatest'] =  $rsCustomerLatest;
$arrTwigVar ['rsCustomerActive'] =  $rs;
$arrTwigVar ['inputSearch'] =  $class->inputText('search');
$arrTwigVar ['btnSearch'] =   $class->inputSubmit('btnSearch',$class->lang['search']);
$arrTwigVar ['btnReset'] =   $class->inputButton('btnReset',$class->lang['resetSearch'], array('add-class' => 'reset-search'));

$arrTwigVar ['searchFilter'] =  implode(', ',$arrLabelFilter);

$arrTwigVar ['inputSelSex'] =  $class->inputSelect('selSex[]', $arrSex, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelJobPosition'] =  $class->inputSelect('selJobPosition[]', $arrJobPosition, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelBusiness'] =  $class->inputSelect('selBusiness[]',$arrBusiness, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelCity'] =  $class->inputSelect('selCity[]',$arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelCountry'] =  $class->inputSelect('selCountry[]',$arrCountry, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelNationality'] =  $class->inputSelect('selNationality[]',$arrNationality, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar ['inputSelMembership'] =  $class->inputSelect('selMembership[]',$arrMembership, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : '';

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 

echo $twig->render('member-directory.html', $arrTwigVar);

?>
