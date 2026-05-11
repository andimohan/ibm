<?php
class ILCMember extends BaseClass
{

	function __construct($meetingType = '1')
	{

		parent::__construct();
		$this->tableName = 'ilc_member';
		$this->tableStatus = 'master_status';
		$this->newLoad = true;

		$this->securityObject = 'ilcMember';


		// join date detail di nonaktifkan, cuma bisa update dr front end

		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey');
		$this->arrData['code'] = array('code');
		$this->arrData['name'] = array('name');
		$this->arrData['statuskey'] = array('selStatus');
		$this->arrData['mobilecode'] = array('selMobileCode');
		$this->arrData['mobile'] = array('mobile');
        $this->arrData['langkey'] = array('selLang');
		$this->arrData['email'] = array('email');


		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'time', 'dbfield' => 'name', 'default' => true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'mobile', 'title' => 'phone', 'dbfield' => 'mobile', 'default' => true, 'width' => 100));
		array_push($this->arrDataListAvailableColumn, array('code' => 'email', 'title' => 'email', 'dbfield' => 'email', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'createdon', 'width' => 100,'default' => true, 'align' =>'center', 'format' => 'date'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
		array_push($this->arrSearchColumn, array('No HP', $this->tableName . '.mobile'));
		array_push($this->arrSearchColumn, array('email', $this->tableName . '.email'));

		$this->includeClassDependencies(array());


		$this->overwriteConfig();
	}

	function getQuery()
	{

		$sql = '
                 select
                     ' . $this->tableName . '.*,
                     ' . $this->tableStatus . '.status as statusname
                 from 
                     ' . $this->tableName . ',
					 ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey';

		$sql .= $this->criteria;

		return $sql;
	}





	function validateForm($arr, $pkey = '')
	{
		$arrayToJs = parent::validateForm($arr, $pkey);

		$name = $arr['name'];

		if (empty($name)) {
			$this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
		} else {

			if ($this->loadSetting('uniqueCustomerName') == 1) {
				$rsCustomer = $this->isValueExisted($pkey, 'name', $name);
				if (count($rsCustomer) <> 0)
					$this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][2]);
			}
		}
		if (empty($arr['mobile'])) {
			$this->addErrorList($arrayToJs, false, $this->errorMsg['phone'][1]);
		}else{
			$rsCust = $this->isValueExisted($pkey, 'mobile', $arr['mobile']);
			if (count($rsCust) <> 0)
				$this->addErrorList($arrayToJs, false, $this->errorMsg['phone'][2]);
		}
		
		
		if (empty($arr['email'])) {
			$this->addErrorList($arrayToJs, false, $this->errorMsg['email'][1]);
		}

		if (isset($arr['email']) && !empty($arr['email'])) {
			$email = $arr['email'];

			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				$this->addErrorList($arrayToJs, false, $this->errorMsg['email'][3]);

			if ($this->loadSetting('uniqueCustomerEmail') == 1) {
				$rsCust = $this->isValueExisted($pkey, 'email', $email);
				if (count($rsCust) <> 0)
					$this->addErrorList($arrayToJs, false, $this->errorMsg['email'][2]);
			}
		}




		return $arrayToJs;
	}
 
	 function afterUpdateData($arrParam, $action){ 
        
        $pkey = $arrParam['pkey'];
		 
        // kalo add user baru
        if ($action == INSERT_DATA){ 
            $this->sendRegistrationEmail($pkey); 
		}
              
    } 
	    
	function sendRegistrationEmail($userkey){
		
        global $twig;
         
		// kirim email 
        $rsCust = $this->getDataRowById($userkey);
		
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];  
          
        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                        ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                                      ); 
        
        $content = $twig->render($this->getLangTemplatePath('email-ilc-registration.html',true,$rsLang[0]['code']), $arrTwigVar); 
  
        $this->sendMail(array(), 'iCommunity Level-Up Club',$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email']));
		
		// kirim WA 
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-ilc-registration.html',true,$rsLang[0]['code']), $arrTwigVar); 
            
			$content = html_entity_decode(strip_tags($content));
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
	
	

	function normalizeParameter($arrParam, $trim = false) {  
        
        // kalo ad mobile code, omit angka 0
        if(isset($arrParam['selMobileCode']) && !empty($arrParam['selMobileCode']))
            $arrParam['mobile'] = ltrim($arrParam['mobile'],0);
        
		$arrParam = parent::normalizeParameter($arrParam, true);
		return $arrParam;
	}
}
