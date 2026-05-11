<?php
class CampaignNewsletter extends BaseClass{
 
   function __construct(){
		
		parent::__construct();

		$this->tableName = 'campaign_newsletter';  
		$this->securityObject = 'CampaignNewsletter'; 
		$this->tableNameDetail = 'campaign_newsletter_detail';
		$this->tableStatus = 'transaction_status';
	    $this->tableLangValue = 'campaign_newsletter_lang';
		$this->tableCity = 'city';
		$this->tablecountry = 'country';
		$this->tableBusinessCategory = 'business_category';
		$this->tableSex = '_sex';
		
		$this->isTransaction = true;
		$this->newLoad = true;
        
//		$this->arrBusinessCategory = array(); 
//        $this->arrBusinessCategory['pkey'] = array('hidBusinessCategoryDetailKey');
//        $this->arrBusinessCategory['refkey'] = array('pkey', 'ref');
//        $this->arrBusinessCategory['businesskey'] = array('selBusinessKey');
       
        $arrDetails = array();
//	   	array_push($arrDetails, array('dataset' => $this->arrBusinessCategory, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
    
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['trdate'] = array('trDate','date'); 
        $this->arrData['agefrom'] = array('ageFrom','number'); 
        $this->arrData['ageto'] = array('ageTo','number'); 
        $this->arrData['sexkey'] = array('selSexKey','raw');  
        $this->arrData['businesskey'] = array('selBusinessKey','raw'); 
        $this->arrData['jobpositionkey'] = array('selJobPositionKey','raw');
        $this->arrData['citykey'] = array('selCityKey','raw'); 
        $this->arrData['countrykey'] = array('selCountryKey','raw'); 
        $this->arrData['nationalitykey'] = array('selNationalityKey','raw'); 
        $this->arrData['membershipkey'] = array('selMembershipKey','raw');
        $this->arrData['langkey'] = array('selLangKey','raw');
        $this->arrData['detail'] = array('txtDetail','raw');
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 150, 'align' =>'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));


        $this->arrSearchColumn = array ();
        array_push( $this->arrSearchColumn, array('code', $this->tableName . '.code'));
        array_push( $this->arrSearchColumn, array('sexname', $this->tableSex . '.name'));
        array_push( $this->arrSearchColumn, array('cityname',$this->tableCity. '.name'));
        array_push( $this->arrSearchColumn, array('countryname',$this->tablecountry. '.name'));

        $this->overwriteConfig();
        $this->includeClassDependencies(array( 
            'Country.class.php',
            'Category.class.php',
            'BusinessCategory.class.php'
        ));  

           
	}
	
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
				'.$this->tableName . ', 
				'.$this->tableStatus.'	
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		 
    }

	  function getDetailWithRelatedInformation($pkey){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*
 
              from
			  	'.$this->tableNameDetail .' 

			  where 
			  	'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
		return $this->oDbCon->doQuery($sql);
	
   }

    
    function normalizeParameter($arrParam, $trim = false){
        
        $arrParam['selSexKey'] = (!empty($arrParam['selSexKey'])) ? json_encode($arrParam['selSexKey']) : '';
        $arrParam['selBusinessKey'] = (!empty($arrParam['selBusinessKey'])) ? json_encode($arrParam['selBusinessKey']) : '';
        $arrParam['selJobPositionKey'] = (!empty($arrParam['selJobPositionKey'])) ? json_encode($arrParam['selJobPositionKey']) : '';
        $arrParam['selMembershipKey'] = (!empty($arrParam['selMembershipKey'])) ? json_encode($arrParam['selMembershipKey']) : '';
        $arrParam['selCityKey'] = (!empty($arrParam['selCityKey'])) ? json_encode($arrParam['selCityKey']) : '';
        $arrParam['selCountryKey'] = (!empty($arrParam['selCountryKey'])) ? json_encode($arrParam['selCountryKey']) : '';
        $arrParam['selNationalityKey'] = (!empty($arrParam['selNationalityKey'])) ? json_encode($arrParam['selNationalityKey']) : '';
        $arrParam['selLangKey'] = (!empty($arrParam['selLangKey'])) ? json_encode($arrParam['selLangKey']) : '';
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
		$arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam;
    }
    
    function sendNewsletterEmail($rsCust,$rsNewsletter){
		
        global $twig; 
		
		// kirim email
		 
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust['name']; 
        $arrTwigVar['CONTENT'] = $rsNewsletter['detail'];
           
//        $content = $twig->render($this->getLangTemplatePath('email-newsletter.html',true,$rsCust['langcode']), $arrTwigVar); 
//        $this->sendMail(array(), $rsNewsletter['name'],$content,array('name' => $rsCust['name'], 'email'=>$rsCust['email'])); 
        
        // kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-newsletter.html',true,$rsCust['langcode']), $arrTwigVar);
			$content = html_entity_decode(strip_tags(htmlspecialchars_decode($content))); // htmlspecialchars_decode khusus newsletter yg dr textarea 
            
            if(!empty($rsCust['mobilecode'])) $rsCust['mobile'] = $rsCust['mobilecode'] . $rsCust['mobile'];
			$this->sendWA($rsCust['mobile'],$content,true);
		}
		 
	}
    
}
		
?>