<?php
#[\AllowDynamicProperties]
class Database {
	var $con;
	var $result;  
    
	protected $transactionCounter = 0;
	protected $onRollback = false;
	 
	function __construct($userid,$password,$dbname,$hostname) { 
  		
        $this->errorLogSession = array();
        $this->errorLog = array(); 
        $this->dbname = '';
        
        $this->onRollback = false;
		$this->connect($userid,$password,$dbname,$hostname); 
	}
 
	function connect($userid,$password,$dbname,$hostname){ 
        
		try {
			$this->con = new PDO('mysql:host='.$hostname.';dbname='.$dbname, $userid, $password); 
            $this->dbname = $dbname;
		}
		catch(PDOException $e) { 
			$this->setLog('error on connect : db :' . $dbname . ', user :' . $userid. ', pass :' . $password ); 
			$this->setLog( $e ); 
			return false;
		}
		
		try {
			$this->con->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
			$this->con->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND,'SET time_zone = \'Asia/Jakarta\'');
		}
		catch(PDOException $e) {
			$this->setLog('error : ' . $e); 
			return false;
		}
		
		return $this->con;
	}
 
	function execute($varsql,$con='') { 
	  
        if (empty($con))
            $con = $this->con; 
        
	    $this->result = $con->exec($varsql); 
        $lastId = $con->lastInsertId();
        
		if($this->result === false) {
		 	  
			$errinfo = $this->con->errorInfo();
			$this->setLog('Invalid SQL command : ' . $varsql . chr(13) . $errinfo[2] .chr(13)); 
			$this->errMsg =  $errinfo[2];  
			$this->con->rollBack();
			die;
		}

		return array('result' => $this->result, 'lastId' =>  $lastId);
	}
 
    function lastInsertId($con=''){
          if (empty($con))
            $con = $this->con; 
        
        return $con->lastInsertId();
    }
    
    function prepare($sql,$con=''){
         
        if (empty($con))
            $con = $this->con; 
        
         return $con->prepare($sql);
    }
 
	function doQuery($varsql,$con='') {
		 
        if (empty($con))
            $con = $this->con;
        
		$this->result = $con->query($varsql);

		if($this->result == false) {
		
			$errinfo = $this->con->errorInfo();
			$this->setLog('Invalid SQL command : ' . $varsql . chr(13) . $errinfo[2] .chr(13)); 
			$this->errMsg =  $errinfo[2];  
			$this->con->rollBack(); 
			die;
		}
		
		$resultSet = $this->result->fetchAll();
		return $resultSet;
	}
 
	 function startTrans($forceReset = false, $log = '') { 
           
             if($forceReset){
                $this->transactionCounter = 0;
                $this->onRollback = false; 
                
                // paksa rollback dulu agarpasti bisa beginTrans
                try{  $this->con->rollback();  } catch(Exception $e){ } 
             }
         
            if ($this->transactionCounter < 0 )
				$this->transactionCounter = 0; 
         
			if ($this->transactionCounter++ == 0) {  
                
                // php 8
                if (!$this->con->inTransaction()) 
				    return $this->con->beginTransaction();
			}  		
			return true;
  	}

	function endTrans($log='') { 
        
        $this->throwIfHasErrorLog();
         
		if ($this->transactionCounter == 1) { 
            $this->transactionCounter = 0;
            $this->con->commit();
        }
		
		$this->transactionCounter--; 
	}
	
    function rollback($log='') {  
        $this->onRollback = true;
        
		if($this->transactionCounter > 0){ 
            $this->transactionCounter = 0; 
            $this->con->rollback(); 
        }   
    }
     
    function getTransactionCounter(){
        return  $this->transactionCounter;
    }
    
/*
    function rollback() {  
		if($this->transactionCounter > 1){ 
            $this->transactionCounter = 0; 
            $this->con->rollback(); 
        } 
        
        $this->transactionCounter = 0;   
    }	 
	 */

    function paramOrder($columnname){
        
        $keywords = array(' ','(',')',';');
        
        // gk bisa pake $this->arrDataListAvailableColumn karena database gk inherit baseclass
        foreach($keywords as $keyword){ 
             if(stripos($columnname, $keyword) !== false) { $columnname = 'pkey'; break; } 
        }
         
        
        /*
        // compability
        if(!isset($this->arrDataListAvailableColumn)){
            if(strpos($columnname, ' ') !== false) { $columnname = 'pkey'; } 
        }else{
            $availableColumn = array_column($this->arrDataListAvailableColumn,'dbfield');
                $this->setLog($availableColumn,true); 
            if (!in_array($columnname, $availableColumn)){ 
                $this->setLog("in 123",true); 
                $columnname = $availableColumn[0];
            }
        }*/
         
        return $columnname;
    }
	  
	function paramString($str,$returnAsStringDelimiter = '') {
        if (is_array($str)){
            $temp = array();
             
            foreach($str as $string)
                array_push($temp, $this->paramString($string));
            
            /*for($i=0;$i<count($str);$i++){
              $temp[$i] = $this->paramString($str[$i]);
            }*/
            
            if(!empty($returnAsStringDelimiter)){
                return (!empty($temp)) ? implode($returnAsStringDelimiter,$temp) : '\'\'';
            }else{
                return $temp;
            }
          
        }
        
		$str = htmlspecialchars($str ?? '');
		$quote = '\'';
		$result = '\'';
		$length = strlen($str);
		for($i = 0; $i < $length; $i++){
			$result = $result . $str[$i];
			if($str[$i] == $quote) {
				$result = $result . $quote;
		  	}
		  	if($str[$i] == '\\'){
				$result = $result . '\\';
		  	}
		}

		$result = $result . $quote;
		return $result;
	}
	 
	 
	function paramDate($dt,$separator='/',$format='Y-m-d H:i') { 
		// sudah support =>  '/' == ' / '
        // $dt format indonesia => DD MM YYYY
        if(empty($dt)) return '0000-00-00';
        
		$strs = explode($separator, $dt); //  agar kompatible dengan ' / ' => "01 "," 02 "," 2022 14:00"
		$day = intval($strs[0]);
		$month = intval($strs[1]);
		
		$strs[2] = trim($strs[2]); // agar kompatible dengan ' / ';
		$strs = explode(" ", $strs[2]);
		$year = intval($strs[0]);
		
        if($day == '00' && $month == '00' && $year == '0000') 
            return '0000-00-00';
        
		$hour = 0;
		$min = 0;
		$sec = 0;
		
		if (isset($strs[1]) && !empty($strs[1])) {
			$strs[1] = trim($strs[1]);
			$strs = explode(":", $strs[1]);
			$hour = intval($strs[0]);
			$min =  intval($strs[1]);
			if(!empty($strs[2]))
				$sec =  intval($strs[2]);
		}
		 
		return '\''. date($format, mktime($hour,$min,$sec, $month, $day, $year )) . '\''; 
	}
	
	function setLog($msg,$alwaysShow = false ,$title = ''){ 
	     
		// sementara
		if(is_array($msg)) $msg = print_r($msg, true);
        
	 	$path = DOC_ROOT.'log/';
		$path .= (DOMAIN_NAME) ? DOMAIN_NAME.'/' : '';
		
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		} 
		
        $filename = (empty($title)) ? md5(DOC_ROOT) : $title; 
        $filename = '['.date('d-m-Y') .'] - '.$filename.'.txt'; 
		$filename = $path.$filename; 
				   
		error_log ($msg.chr(13),3,$filename);
	}
    
    function resetErrorLog(){ 
            $this->errorLog = array();
    }
       
	function addErrorLog($condition,$label,$throwIfHasErrorLog = false){     
        
        if (empty($this->errorLogSession))
            $this->startNewErrorLogSession();
            
        array_push($this->errorLog,array("valid" => $condition, "message" => $label));  
        
        if($throwIfHasErrorLog)
            $this->throwIfHasErrorLog();
	}
    
    function startNewErrorLogSession(){ 
        array_push($this->errorLogSession, $this->errorLog);
        $this->errorLog = array(); 
    }
    
    function getErrorLog (){
        return $this->errorLog;
    } 
    
    function throwIfHasErrorLog(){ 
        
        $errMsg = array();
        foreach ($this->errorLog as $err){
            if (!$err['valid'])    
                 array_push($errMsg,array('valid'=>false, 'message' => $err['message']));
        }

        //$this->setLog("error " . json_encode($errMsg));
        
        if (!empty($errMsg)){ 
            $this->errorLog = $errMsg; // remove all "true" error
            throw new Exception();
            die;
        } 
        
        // tambahan flag rollback
        if($this->onRollback){
            throw new Exception();
            die;
        }
            
         
    }
     
}

#[\AllowDynamicProperties]
class BaseClass{
	  
function __construct(){  
    
	$this->oDbCon = $GLOBALS['oDbCon'];  
	  
	$this->tableName = '';
	$this->tableStatus = '';
	$this->tableTag = 'tag';
	$this->tableSex = '_sex';
	$this->tableAssetType = 'asset_type';
	$this->tableReligion = 'religion';
	$this->tableMaritalStatus = 'marital_status';
	$this->tableReadLog = 'read_log'; 
    $this->tableCompany = 'company'; 
    $this->tableFuelType = '_fuel_type';
    $this->tableMultipleAddress = 'multiple_address_detail';
    $this->tableCustomCodeResetType = 'custom_code_reset_type';
    $this->tablePaymentMethod = 'payment_method';
    $this->tableLocation = 'location';
    $this->tableEmployee = 'employee';
    $this->tablekey = 'tablekey'; 
    $this->tableSettings = '_setting'; 
    $this->tableUserSettings = '_user_setting'; 
    $this->tableUnit = 'item_unit';
    $this->tableCustomer = 'customer';
    $this->tableAlias = 'table_alias'; // buat dummy saja
    $this->tableFixedUnitConversion = 'fixed_unit_conversion';
    $this->transactionLog = 'transaction_log';
    $this->transactionLogAction = 'transaction_log_action';
    $this->transactionLogDetail = 'transaction_log_detail';
    $this->tableCustomCode = 'custom_code';
    $this->tableCustomCodeCounter = 'custom_code_counter'; 
	$this->tableWarehouse = 'warehouse';
	$this->tableBusinessUnit = 'business_unit';
    $this->tableSecurityObject = 'security_object';
    $this->tableUserSecurityObject = 'user_security_object';
    $this->tag = 'tag';
    $this->tagDetail = 'tag_detail';
    $this->tableSettingListHeader = '_setting_form_list_header';
    $this->tableSettingListDetail = '_setting_form_list_detail';
    $this->tableThemeSetting = 'user_themes_settings';
    $this->tablePlanType = '_plan_type';
    $this->tableLang = 'lang';
    $this->tableLangValue = 'lang_detail';
    $this->isTransaction = false;
        
	$this->sqlQuery = ''; 
	$this->sqlTotalRowsQuery = '';
	$this->domain = strtolower(DOMAIN_NAME);     
    $this->domainFolder =  DOMAIN_FOLDER ;     
	$this->adminFolder = 'admin';
	$this->defaultDocAdminPath = DOC_ROOT . $this->adminFolder; 
	$this->defaultURLAdminPath = HTTP_HOST . $this->adminFolder; 
	
	$this->pageSelfLink = substr(HTTP_HOST,0,strlen(HTTP_HOST)-1);
	
	if(isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']))
			$this->pageSelfLink .= $_SERVER['REQUEST_URI'];
	 
	$this->uploadTempDoc  =  UPLOAD_TEMP_DOC ; // pake ini utk upload file, pake '/../' gk bisa
	$this->uploadTempDocShort  = '/../_temp/' .$this->domainFolder  ; // untuk phpThumb, agar tidak terlihat path full. 
	$this->uploadTempURL  = HTTP_HOST. '../_temp/' .$this->domainFolder  ;
	$this->defaultDocUploadPath= DEFAULT_DOC_UPLOAD_PATH; //DOC_ROOT. '../_upload/' .$this->domainFolder  ;  
	$this->defaultURLUploadPath= HTTP_HOST. '../_upload/' .$this->domainFolder  ; 
	
	$this->phpThumbURLSrc = '/../_upload/' .$this->domainFolder ;
    $this->phpThumbPath = '/phpthumb/phpThumb.php'; 

    $this->fckURLUploadPath = HTTP_HOST.'uploadeditor/'.$this->domainFolder;			// buat simpan path relatif img (fckeditor)
	$this->fckDOCUploadPath = DOC_ROOT.'uploadeditor/'.$this->domainFolder;				// buat simpan path relatif img (fckeditor)
    
	$this->adminTotalRowsPerPage = $this->loadSetting('adminTotalRowsPerPage'); 
	 
    // kalo pake S3
    $this->initStorage();
    
	/* Create default folder for upload */
	if (!file_exists($this->defaultDocUploadPath.'setting')) 
			mkdir($this->defaultDocUploadPath.'setting', 0755, true); 
	if (!file_exists($this->defaultDocUploadPath.'setting/companyLogo')) 
			mkdir($this->defaultDocUploadPath.'setting/companyLogo', 0755, true); 


	
	$this->defaultJsPath = HTTP_HOST. 'include/js-1.9/' ;	
	$this->defaultDocJsPath = DOC_ROOT. 'include/js-1.9/' ;		
	$this->defaultImgPath = HTTP_HOST. 'include/img/' ;		
	$this->adminCssPath = HTTP_HOST. 'include/css-1.4/' ; 
	
    $this->secretKey = SECRETKEY;
    $this->appsSecretKey = (defined('APP_SECRETKEY')) ? APP_SECRETKEY : '';
    
	$this->loginSession = LOGIN_SESSION;
	$this->loginAdminSession = LOGIN_ADMIN_SESSION; 
	 
    $this->templateName = (!file_exists(DOC_ROOT . 'template/'. $this->domainFolder)) ? 'default/' : $this->domainFolder ; 
    
	$this->templateDocPath = DOC_ROOT . 'template/'. $this->templateName;  
	$this->templateURLPath = HTTP_HOST . 'template/' . $this->templateName;  
      
	$this->templateCssPath = $this->templateURLPath . 'css/'; 
	$this->templateSwfPath = $this->templateURLPath . 'swf/'; 
	$this->templateImgPath = $this->templateURLPath . 'img/';   
	$this->templateJsPath = $this->templateURLPath . 'js/'; 
	
	$this->arrDiscountType = array();
	$this->arrDiscountType[1] = 'IDR';
	$this->arrDiscountType[2] = '%';
	 
    $this->tableNeedToBeCopyOnCancel = array(); 
    $this->usingAutoCode = false;
    
	$this->criteria = '';	
    $this->filterDataByCompany = true;
    
    // DATA FOR FORM 
    $this->rs = array();
    $this->allowedStatusForEdit = array(1);
         
	// TAG 
    $this->shadowClass = array(); 
    
	// URL FILTER
	$this->arrSearch = array("%","+","?"," ","/","\\","#","<",">","&amp;","&");
	$this->arrReplace = array("%25","%2B","","-","-","-","-","-","","");
    
    $this->SN_SPLIT_REGEX = '/[,\s]+/'; // utk split SN, di taro di clas biar kebaca ketika diakses API
    
    // LOADING ICON 
    $this->loadingIcon = '<span class="loading-icon fas fa-spinner fa-spin"></span>';
	 
    $this->langCode = 'id';
    $this->setActiveLang();
     
    // FILTER DATA 
    $this->filterCriteria = array();
    array_push($this->filterCriteria, array('title' => $this->lang['status'], 'field' => 'statuskey')); 
     
    
    if(!isset($_SESSION['targetID'])) 
        $_SESSION['targetID'] = 0;
    
    // GRAPH
    $this->graphLineColorSet = '  { 0: {color: \'#008eb3\' }, 
                                  1: {color: \'#e9656b\' },  
                                  2: {color: \'#515898\' },
                                  3: {color: \'#f1ca3a\' },
                                  4: {color: \'#f542ce\' },
                                  5: {color: \'#e7711b\' },
                                  6: {color: \'#6f9654\' }
                                }  ';
    
    $this->graphPieColorSet = '   {
                                    0: { color: \'#FF8264\' },
                                    1: { color: \'#43459d\' },
                                    2: { color: \'#f1ca3a\' },
                                    3: { color: \'#6f9654\' },
                                    4: { color: \'#f542ce\' },
                                    5: { color: \'#e7711b\' },
                                  }
    ';
    
    $this->graphColorSet = array('#008eb3', '#e7711b', '#f1ca3a', '#6f9654', '#f8b786', '#43459d','#6f9654','#DC3912','#FF9900','#109618','#990099','#3B3EAC', '#3B3EAC','#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395', '#994499', '#22AA99', '#AAAA11', '#6633CC', '#E67300', '#8B0707', '#329262', '#5574A6','#3B3EAC');
       
    // PRINT
    $this->printSetting = array();
    $this->printSetting['defaultStyle'] = ' <style>    
                                            .b {font-style:bold}
                                            .i {font-style:italic} 
                                            .text-gray-dim{color:#696969}
                                            .title {font-weight:bold; text-align:center; font-size:1.5em}
                                            .subtitle { text-align:center; font-size:1.2em}
                                            .header-row-header {font-weight:bold; width: 120px;}
                                            .col-label {font-weight: bold;} 
                                            .col-header td{border-top:1px solid #333; border-bottom:1px solid #333; font-weight:bold;} 
                                            .table-transaction {border-bottom:1px solid #333;} 
                                            .table-transaction .subtotal {font-weight:bold;}
                                            .col-border-bottom{border-bottom:1px solid #dedede}  
                                            .sign .sign-col {width: 150px; height:100px;}
                                            .sign .sign-col-space {width:20px}
                                            .sign .sign-name {border-bottom:1px solid #333; font-style:italic} 
                                            .footnote {font-size:0.9em; font-style:italic} 
                                            .last-border-bottom {border-bottom:1px solid #333} 
                                            .no-border {border:0px solid #fff} 
                                            </style>';
    $this->printSetting['fileNameLength'] = 200; 
    $this->printDocWidth = '680';
    
    $this->userkey = (isset($_SESSION[$this->loginAdminSession]['id']) )  ? base64_decode($_SESSION[$this->loginAdminSession]['id']) : 0;
    
    $this->warehouseMovementRules = array();
    $this->warehouseMovementRules[1] = 'Sebagian';
    $this->warehouseMovementRules[2] = 'Seluruhnya';
    
    $this->arrExcludeFieldOnMultiDetail = array('pkey', 'hidDetailKey');
    $this->uploadQueue = array(); // untuk upload file/image
    //$this->removeFileQueue = array(); // untuk upload file/image
    
    $this->arrDataLang = array(); 
    $this->arrDataLang['pkey'] = array('hidDetailKey');
    $this->arrDataLang['refkey'] = array('pkey','ref');
    $this->arrDataLang['reftabletype'] = array('refTableType');
    $this->arrDataLang['reflangkey'] = array('hidLangKey');
    $this->arrDataLang['columnname'] = array('columnName');
    $this->arrDataLang['value'] = array('langValue','noformat');

	$this->arrDataFile = array(); 	 
    $this->arrDataFile['pkey'] = array('hidDetailFileKey');
    $this->arrDataFile['refkey'] = array('pkey','ref');
    $this->arrDataFile['file'] = array('item-file-uploader',array('datatype' => 'file', 'mandatory' => true));
	$this->arrDataFile['size'] = array('item-file-size');
    
    $this->arrDataImage = array(); 	 
    $this->arrDataImage['pkey'] = array('hidDetailImageKey');
    $this->arrDataImage['refkey'] = array('pkey','ref');
    $this->arrDataImage['file'] = array('item-image-uploader',array('datatype' => 'image', 'mandatory' => true));
    $this->arrDataImage['size'] = array('item-image-size');

    $this->activeModule = array();
    
    $this->totalUploadSize = 0;
    
    $this->uploadQRFolder = 'qr'; // dummy
    
    $this->isImageExt = array('jpg','jpeg', 'png', 'gif', 'webp', 'svg');
    
    //$this->updateTablekeyStatus();
  
 }
 
function masterConn(){ 
    return new Database(PS_DB['dbuser'],PS_DB['dbpass'],PS_DB['dbname'],PS_DB['host']);
}


function archiveConn(){ 
    // cari nama database utk archive
    
    if (defined('ARCH_DB'))  
       return new Database(ARCH_DB['dbusername'],  substr(ARCH_DB['dbpass'],4) ,ARCH_DB['dbname'],PS_DB['host']);
    
    // kalo gk ad database archive, berarti masih pake database utama
    return $this->oDbCon ;
}


 function getQuery(){
		$sql = 'select * from ' . $this->tableName . ' where 1=1 ' .$this->criteria ;  
        $sql .=  $this->getCompanyCriteria();
        return $sql;
 }
 
 function setCriteria($criteria){
	 $this->criteria = $criteria;
 }
 
 function generateEmail($arrTemp){
        global $twig;
        
        $arrDefault = array();
        $arrDefault['HTTP_HOST'] = HTTP_HOST;
        $arrDefault['companyLogoDocPath'] = 'setting/emailLogo/'.$this->loadSetting('emailLogo'); 
        $arrDefault['companyName'] = $this->loadSetting('companyName');
        
        $arrTemp = array_merge($arrTemp,$arrDefault);
        return $twig->render('email-template.html', $arrTemp); 
     
        /*
        $file=  $this->templateURLPath . 'email-template.html';  
        $invoice =  file_get_contents($file);
    
        return $invoice; 
        */
        /*
        //pastikan dr http agar selalu bisa diakses
        //$this->defaultURLUploadPath = str_replace('https://','http://',$this->defaultURLUploadPath);
        $HTTP_HOST = str_replace('https://','http://',HTTP_HOST); 
     
        $email = $this->loadSetting('emailTemplate');
     
        $patterns = array();
        $patterns[count($patterns)] = '/({{COMPANY_NAME}})/'; 
        $patterns[count($patterns)] = '/({{WEBSITE_URL}})/';  
        $patterns[count($patterns)] = '/({{COMPANY_LOGO}})/';  

        $phpthumbhash = 
            
        $replacement = array();
        $replacement[count($replacement)] = $this->loadSetting('companyName'); 
        $replacement[count($replacement)] = $this->loadSetting('sitesName');  
        $replacement[count($replacement)] = '<img src="'.$HTTP_HOST.'download.php?filename='.$this->defaultDocUploadPath.'setting/emailLogo/'.$this->loadSetting('emailLogo').'" />';
              
        $email = preg_replace($patterns, $replacement, $email);  
      
		return $email; 
        */
 }
 
	// model convertForCombobox yg baru
    function generateComboboxOpt($opt = array(),$queryOpt = array(),$preselected='',$relOpt = array()){
	       
		// data
		// select query field
		// pair key and value
		
		if(empty($opt))
			$opt = array('label' => 'name', 'value' => 'pkey');
		
		$label =  (!isset($opt['label'])) ? 'name' : $opt['label'];
		$value =  (!isset($opt['value'])) ? 'pkey' : $opt['value'];
		$queryLabel =  (!isset($opt['queryLabel'])) ? 'name' : $opt['queryLabel'];
		$queryValue =  (!isset($opt['queryValue'])) ? 'pkey' : $opt['queryValue'];
		
		// kalo dikirim data, pake rs yg dikirim
		$criteria = (isset($queryOpt['criteria'])) ? $queryOpt['criteria'] : '';
		$order = (isset($queryOpt['order'])) ? $queryOpt['order'] : '';
		
		$rs = (!isset($opt['data'])) ? $this->searchDataRow( array($queryLabel,$queryValue), $criteria,$order) : $opt['data'];
 
		$returnVal = array();
		 
        // untuk index pertama seperti : "-- Pilih Kategori --"
		if (!empty($preselected))
			 $returnVal[0] = $preselected;
			  
        foreach($rs as $row){
             
            $opt = array('label' => $row[$label]); 
            
            // loop setiap byk nya rel 

            $arrOpt = array(); 
            foreach($relOpt as $relKey=>$relRow)     
                $arrOpt[$relKey]  = $row[$relRow]; 
            
            $opt['rel'] = $arrOpt;
             
            $returnVal[$row[$value]] = $opt; 
        }
        
        unset($resultSet);
          
		return $returnVal;
   }
   
	
    function convertForCombobox($rs,$value,$label,$preselected='',$relOpt = array()){
	       
		$returnVal = array();
		 
        // untuk index pertama seperti : "-- Pilih Kategori --"
		if (!empty($preselected))
			 $returnVal[0] = $preselected;
			  
        foreach($rs as $row){
             
            $opt = array('label' => $row[$label]); 
            
            // loop setiap byk nya rel 

            $arrOpt = array(); 
            foreach($relOpt as $relKey=>$relRow)     
                $arrOpt[$relKey]  = $row[$relRow]; 
            
            $opt['rel'] = $arrOpt;
             
            $returnVal[$row[$value]] = $opt; 
        }
        
        unset($resultSet);
          
		return $returnVal;
   }
   
   function getAllStatus ($tableStatus = '') { 
	 	
		if ($tableStatus == ''){
			$tableStatus = $this->tableStatus ;
		}
		
		$sql = ' select  * from  '.$tableStatus .' order by pkey asc ';
			 
		return $this->oDbCon->doQuery($sql); 
	 
	}   
	      /*
   function getAllTag () { 
	  
		$sql = '
				select 
					*
				from 
					tag
				order by
					pkey asc
			';

		$rs = $this->oDbCon->doQuery($sql); 
	 
        for($i=0;$i<count($rs);$i++){  
            $this->shadowClass[$rs[$i]['pkey']] =  $rs[$i]['shadowclass'];
        }
       
       return $rs;
	}   
*/
    
    function getAllTag () { 
	    if (empty($this->tableName)) return; 
        
		$sql = '
				select 
                    '. $this->tag.'.hexcolor,
                    '. $this->tag.'.shadowclass,
                    '.$this->tagDetail.'.tagname,
                    '.$this->tagDetail.'.pkey,
                    '.$this->tagDetail.'.tagkey 
				from 
                    '. $this->tag.', 
					'.$this->tagDetail.'
                where 
                    '. $this->tag.'.pkey = '.$this->tagDetail.'.tagkey and
                    tablename = '.$this->oDbCon->paramString($this->tableName).' 
				order by
					orderlist asc,
                    '.$this->tagDetail.'.pkey asc
			';  
        
		$rs = $this->oDbCon->doQuery($sql);
        
        if (!empty($rs)){ 
            for($i=0;$i<count($rs);$i++)  
                $this->shadowClass[$rs[$i]['tagkey']] =  $rs[$i]['shadowclass'];
       
            return $rs;
        }
        
        // kalo kosong, tambah default 
        $sql = 'select * from ' . $this->tag;
        $rsTag =  $this->oDbCon->doQuery($sql); 

        try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 

            for($i=0;$i<count($rsTag);$i++){
                 $sql = '
                        INSERT into
                            '.$this->tagDetail.' (tablename,tagkey,tagname,orderlist)
                        VALUES
                            ('.$this->oDbCon->paramString($this->tableName).', '.$rsTag[$i]['pkey'].','.$this->oDbCon->paramString($rsTag[$i]['tagname']).','.($i+1).'  )';

                $this->oDbCon->execute($sql); 
            } 

            $this->oDbCon->endTrans();  
            return $this->getAllTag();

        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	 
	 
	}   
    
	function getStatusById ($statuskey, $tableStatus= ''){
        $tableStatus = (empty($tableStatus)) ? $this->tableStatus : $tableStatus;
		$sql = 'select * from '.$tableStatus.' where pkey in(' . $statuskey. ')' ;
		return $this->oDbCon->doQuery($sql); 
	}

    function getSex (){
		$sql = 'select * from '.$this->tableSex ;
		return $this->oDbCon->doQuery($sql); 
	}
        
	function getAssetType ($id = array()){
		$sql = 'select * from '.$this->tableAssetType . ' where 1=1';
		if (!empty($$id))
			$sql .= ' and '.$this->tableAssetType . '.pkey in (' . $this->oDbCon->paramString($id,',').')'; 
			
		return $this->oDbCon->doQuery($sql); 
	}
    
    function getFuelType (){
		$sql = 'select * from '.$this->tableFuelType.' order by name asc';
		return $this->oDbCon->doQuery($sql); 
	}
    
    function getReligion (){
		$sql = 'select * from '.$this->tableReligion .' order by name asc';
		return $this->oDbCon->doQuery($sql); 
	}
    
    function getMaritalStatus (){
		$sql = 'select * from '.$this->tableMaritalStatus;
		return $this->oDbCon->doQuery($sql); 
	}
    
// =============================================CHANGE STATUS ======================================== //
	
  function proceedUpdateStatus($status,$rsHeader,$copy){
      // untuk dioverwrite di subclass


        switch ($status){ 
            case 2 : if ($rsHeader[0]['statuskey'] < $status ){ 
                        $this->confirmTrans($rsHeader); 
                        $this->afterConfirmTrans($rsHeader);
                    }else{ 
                        $this->backConfirmTrans($rsHeader); 
                        $this->afterBackConfirmTrans($rsHeader);
                    }
                     break; 
            case 3 : $this->closeTrans($rsHeader); 
                     $this->afterCloseTrans($rsHeader); 
                     break; 
            case 4 : $this->cancelTrans($rsHeader,$copy);
                     $this->afterCancelTrans($rsHeader);
                     break;  
        }
  }
    
  function validateStatus($status,$rsHeader,$autoChangeStatus){
      // untuk dioverwrite di subclass
      
            switch ($status){
                case 1 : $this->validateInput($rsHeader); 
                          break;
                case 2 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateConfirm($rsHeader);
                         else
                            $this->validateBackConfirm($rsHeader); 
                          break;
                case 3 : $this->validateClose($rsHeader); 
                          break;
                case 4 : $this->validateCancel($rsHeader, $autoChangeStatus);
                          break; 
            }  
  }   
    
  function changeTransactionStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
      
        if (!is_numeric($status))  die;
          
        $rsHeader = $this->getDataRowById($id); 
         
      	try{ 
            if(!$autoChangeStatus){  
                
                // dipindahkan kebawah, agar tetep bisa proses dari script / cron
                if (empty($_SESSION[$this->loginAdminSession]['id']))  die; 
                
                $security = new Security();
                if(!$security->isAdminLogin($this->securityObject,$status,false)) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true);  
                
                
                // kalo ubah back status, user harus punya akses status yg skrg
                // sementara testing dulu di beberapa domain
                $useBackStatusValidation = $this->loadSetting('useBackStatusValidation');
                if ($useBackStatusValidation == 1){
                    if ($status < $rsHeader[0]['statuskey']){ 
                        if(!$security->isAdminLogin($this->securityObject,$rsHeader[0]['statuskey'],false)) 
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true); 
                    }
                }
                
                // kalo ad criteria tambahan, abaikan
                /* if (isset($this->privilegesCriteria))
                    $this->privilegesCriteria = false;*/
                
            }
            
            
            // jika status bkn status sendiri dan bukan status terakhir (status cancel) 
            if($this->isCancelStatus($rsHeader[0]['statuskey']))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[221],true);   
    
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
   
        }catch(Exception $e){ 
 		     return $this->getErrorLog(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 

		try{ 
            
            // ================== VALIDATION 
            if (!$ignoreValidation) 
                $this->validateStatus($status,$rsHeader,$autoChangeStatus); 
       
             
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
             
            
            // ================== VALIDATION OK !
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					  
      
            $this->proceedUpdateStatus($status,$rsHeader,$copy);
         

			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id); 
            $this->oDbCon->execute($sql);  
             
            $this->setTransactionLog($status,$id,'',$reason);
            
            $this->afterStatusChanged($rsHeader);
            
			$this->oDbCon->endTrans();  
			
			$this->afterCommitChangeStatus($id);
			
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){ 
            
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        return $this->getErrorLog(); 
  }   
    
  function changeStatus($id, $newStatus, $reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){ 
		
       if (!is_numeric($newStatus))  die;
      
       if ($this->isTransaction)
           return $this->changeTransactionStatus($id,$newStatus,$reason,$copy, $autoChangeStatus, $ignoreValidation);
        
		try{
            
			$rs = $this->getDataRowById($id);
			   
            try{  
                
                if(!$autoChangeStatus){  
                    $security = new Security();
                    if(!$security->isAdminLogin($this->securityObject,$newStatus,false))  
                        $this->addErrorLog(false,'<strong>'.$rs[0]['code'].'.</strong> '.$this->errorMsg[252],true);   
                }
                 
                // jika status bkn status sendiri dan bukan status terakhir (status cancel)
                if ($rs[0]['statuskey'] == $newStatus) 
                    $this->addErrorLog(false,'<strong>'.$rs[0]['code'].'.</strong> '.$this->errorMsg[224],true);   

            }catch(Exception $e){ 
                 return $this->getErrorLog(); 
                //$this->addErrorList($arrayToJs,false,$e->getMessage());
            }		
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
									 
			$sql = '
					UPDATE
						'.$this->tableName.'
					SET
						statuskey = '.$this->oDbCon->paramString($newStatus).'	
					WHERE
						pkey = '.$this->oDbCon->paramString($id) ;   	
			
			$this->oDbCon->execute($sql); 
            
            $rsStatus = $this->getStatusById ($newStatus); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);
            
            $this->afterStatusChanged($rs);
            
			$this->oDbCon->endTrans();  
            
            $this->afterCommitChangeStatus($id);
	
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorLog(false,$e->getMessage()); 
		}		
				 
 		return $this->getErrorLog(); 
		 
		
	}
	
    function afterStatusChanged($rsHeader){ }

    function afterPrintTransaction($rsHeader){
        $arrayToJs =  array();
			
		try{
			  
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
									   
            $this->setTransactionLog(PRINT_DATA,$rsHeader[0]['pkey']);  
            
			$this->oDbCon->endTrans();  
	 
	    } catch(Exception $e){
			$this->oDbCon->rollback();  
		}		
				 
 		return $arrayToJs; 
    }
    
	function validateInput($rsHeader){ 
        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[202],true);   
		//$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[202]); 
	 }
	 
	 
    function validateBackConfirm($rsHeader){ 
    
    } 
     
	function validateConfirm($rsHeader){  
		if($rsHeader[0]['statuskey'] <> 1) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[203],true);   
    }
	 
	  
	function validateClose($rsHeader){  
		if($rsHeader[0]['statuskey'] <> 2) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[204],true);   
	 }
	  
	 
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
      
        
        if ( !$autoChangeStatus ) {
            if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
        }  
         
        //cek status transaksi selanjutnya sudah konfrimasi, selesai, atau gk
        //JANGAN update status field "isLinked" karena beda. harus tetep selalu cek manual
  
        if (isset($this->arrLinkedTable) && !empty($this->arrLinkedTable)){ 
            for($i=0;$i<count($this->arrLinkedTable);$i++){
                $table = $this->arrLinkedTable[$i]['table'];
                $field = $this->arrLinkedTable[$i]['field'];
                $statuskey = (isset($this->arrLinkedTable[$i]['statuskey'])) ? $this->arrLinkedTable[$i]['statuskey'] : '2,3' ;
                $statusCriteria = (!empty($statuskey)) ?  ' and '.$table.'.statuskey in ('.$statuskey.')' : '';
                          
                $sql = 'select pkey,code from '.$table.' where ' . $field . ' = ' . $this->oDbCon->paramString($pkey) .$statusCriteria.' limit 1';
                $rsLink = $this->oDbCon->doQuery($sql);
                
                if (!empty($rsLink))
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' <strong>'.$rsLink[0]['code'].'</strong>',true);  
                 
            } 
        }  
	 }
    
    function confirmTrans($rsHeader){
        
    }
    
    function backConfirmTrans($rsHeader){
        
    }
    
    function afterBackConfirmTrans($rsHeader){
        
    }
	 
     function afterConfirmTrans($rsHeader){ 
        $user = base64_decode($_SESSION[$this->loginAdminSession]['id']); 
        $sql = 'update ' .$this->tableName.' set  confirmedby = '.$this->oDbCon->paramString($user).', confirmedon = now() where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
        $this->oDbCon->execute($sql);  
     }
    
     function closeTrans($rsHeader){
         
     }
    
     function afterCloseTrans($rsHeader){
         
     }
    
   	function cancelTrans($rsHeader,$copy){  
        
		$id = $rsHeader[0]['pkey'];
		  	   
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    
     function afterCancelTrans($param){ 
        $pkey = (is_array($param)) ? $param[0]['pkey'] : $param; 
        $sql = 'update ' .$this->tableName.' set code = concat(code,\' [\',pkey,\']\') where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);  
     }
    
	 function copyDataOnCancel($id){ 
        $arrReturn = $this->duplicateData($id,true);
         
        $pkey = $arrReturn['pkey'];
        $oldCode = $arrReturn['prevCode'];
         
        $this->afterAddDataOnCopy($pkey,$id); 

        return $pkey;
	}
    
    function validateDuplicateData($rsHeader){  
        //$this->addErrorLog(false,$this->errorMsg[218],true);    
        
         
        // kalo data yg hasil dr link, harusny gk boleh diduplikasi, karena pengaruh ke data lainnnya...
         
        
        if(!$this->isCancelStatus($rsHeader[0]['statuskey']))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[219],true);  
        
//        $allStatus = $this->getAllStatus();
//        if($rsHeader[0]['statuskey'] != count($allStatus)){
//            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[219],true);  
//        }
         
        if(isset($rsHeader[0]['islinked']) && $rsHeader[0]['islinked'] == 1){
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[904],true);  
        }
        
    }
    
    function isCancelStatus($statuskey){ 
        $rsAllStatus = $this->getAllStatus(); 
        return ($statuskey ==  $rsAllStatus[count($rsAllStatus)-1]['pkey'] ) ? true : false;
    }
    
    function duplicateData($id, $force=false, $options = array()){ 
         
        // force, patokan dr menu duplikasi data tau dr batal dan copy 
        
        $rsHeader = $this->getDataRowById($id);
        $oldCode = 	$rsHeader[0]['code']; 
        $revCtr = 	(isset($rsHeader[0]['revctr'])) ? ($rsHeader[0]['revctr']+1) : 1; 

        $pkey = 0;
        
        try{ 
            
            if(!$force){
                $this->validateDuplicateData($rsHeader);  
                $this->throwIfHasErrorLog();  
            }
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
         
			$data = '';
			 
            $reuseDeletedCode = (isset($options['reuseCode'])) ? $options['reuseCode'] : $this->loadSetting('reuseDeletedCode');
            
            if ($reuseDeletedCode == '')
                $reuseDeletedCode = 0;
         
			// HEADER
			$pkey = $this->getNextKey($this->tableName);
         
            if ($reuseDeletedCode){ 
                $usecode =  $oldCode;
                //$sql = 'update ' .$this->tableName.' set code = concat(code,\'-R\',pkey) where pkey = ' . $id;
                //$this->oDbCon->execute ($sql);	
            }else{  
                
                $customCode = (isset($rsHeader[0]['customcodekey'])) ? $rsHeader[0]['customcodekey'] : '';  
                
                $rsTempHeader = $rsHeader[0]; 
                if (isset($rsTempHeader['trdate']))
                    $rsTempHeader['trDate'] = $this->formatDBDate( $rsTempHeader['trdate'],'d / m / Y' ); // pake trDate, gk bisa trdate karena beda arrParam nya
                 
			     $usecode =  $this->getNewCustomCode($rsTempHeader, $customCode);  
				 $usecode = $usecode[0]; // yang baru ad 2 dimensi
            }
         
			$sql = 'show columns from ' .$this->tableName ;
			$rsColumnsName = $this->oDbCon->doQuery ($sql); 
			  
			$rsHeader[0]['pkey'] = $pkey;
			$rsHeader[0]['code'] = $usecode;
			$rsHeader[0]['revctr'] = $revCtr;
			$rsHeader[0]['statuskey'] = 1;
           
			  
			for ($i=0;$i<count($rsColumnsName);$i++){
                
                if ($rsColumnsName[$i]['Field']  == 'confirmedby') 
                    $rsHeader[0][$rsColumnsName[$i]['Field']] = 0; 
                
                if ($rsColumnsName[$i]['Field'] == 'confirmedon') 
                    $rsHeader[0][$rsColumnsName[$i]['Field']] = '0000-00-00'; 
                
				//$data .=  $this->oDbCon->paramString($rsHeader[0][$rsColumnsName[$i]['Field']]);
				// jgn pake paramstring, nanti & makin panjang &amp; nya
				$data .=  '\''.addslashes($rsHeader[0][$rsColumnsName[$i]['Field']]).'\'';  
			
				if ($i <> count($rsColumnsName) - 1)
				  $data .= ',';  
				  
			}
			 
			
			$sql = 'insert into ' .$this->tableName.' values ('.$data.')';
            $this->oDbCon->execute ($sql);	
			
				
			//DETAIL  
            //$this->setLog($this->arrData);
            if (isset($this->arrData) && isset($this->arrData['pkey'][1]['dataDetail']))  
                $this->updateDetailTablesOnCopyV2($id,$pkey,$this->arrData,$pkey);
            else
                $this->updateDetailTablesOnCopy($id,$pkey,$this->tableNeedToBeCopyOnCancel);
            
            // copy file jika ada
			// sementara yg arrData saja dulu
			
			$arrDataType = array('image','file');
			// cari semua folder yg tipenya sama
			foreach($this->arrData as $row){
			   
				if (!isset($row[1]['datatype']) ) continue;

				$dataType = $row[1]['datatype'];
				$folderPath = $row[1]['uploadFolder'];
				if(!in_array($dataType,$arrDataType)) continue;

                
                if($this->useStorage('S3')){ 
                     $this->setLog("batal copy file blm kecopy",true);
                }else{
                    $sourcePath = DEFAULT_DOC_UPLOAD_PATH.$folderPath.$id.'/';
                    $destinationPath = DEFAULT_DOC_UPLOAD_PATH.$folderPath.$pkey.'/';

                    //copy semua file 
                    $this->fullCopy($sourcePath, $destinationPath); 
                }
				 
			} 
            
            
            $this->setTransactionLog(INSERT_DATA,$pkey,'Duplikasi dari ' . $oldCode);	
            
            if(!$force){
                // jika duplikasi manual dr menu 
                $rsHeader = $this->getDataRowById($pkey);
                $this->afterDuplicateData($rsHeader);   
            }
            
			$this->oDbCon->endTrans();  
	 
	    } catch(Exception $e){
			$this->oDbCon->rollback();  
		}		
        
        return array('pkey'=> $pkey, 'prevCode' => $oldCode, 'errorLog' => $this->getErrorLog());
        
    }
    
    function afterDuplicateData($rsHeader){
        
    }
	
	
	function getUploadedFilesPath($id,$arrData=array(),$withIdPath = true, $firstCall = true){
		
		if ($firstCall && empty($arrData)) $arrData = $this->arrData;
		
		$arrDataType = array('image','file');
		
		$arrPath = array();
		
		// cari di header
		foreach($arrData as $row){
			
			// kalo jenisnya pkey dan ad detail
			if ($row[0] == 'pkey' && isset($row[1]['dataDetail'])){
				
				foreach($row[1]['dataDetail'] as $detailArrData){ 
					$detailArrPath = $this->getUploadedFilesPath($id, $detailArrData['dataset'],$withIdPath, false);
					$arrPath = array_merge($arrPath,$detailArrPath);
				}
				
				continue;
			}
			
			if (!isset($row[1]['datatype']) ) continue;
			
			$dataType = $row[1]['datatype'];
			$folderPath = $row[1]['uploadFolder'];
			if(!in_array($dataType,$arrDataType)) continue;
			 
            
			$filePath = ($this->useStorage('S3')) ?  $folderPath  : DEFAULT_DOC_UPLOAD_PATH.$folderPath; 
            if($withIdPath) 
                $filePath .= $id.'/';
            
			array_push($arrPath, $filePath);
				
		}
		
		return $arrPath;
	}
	
	function deleteFiles($id){
		// cari dulu semua path
		// hanya berlaku utk model upload file baru
		
		$arrPath = $this->getUploadedFilesPath($id);
        
		foreach($arrPath as $row){
            if($this->useStorage('S3')){
                //
                
            }else{
                $this->deleteAll($row,true);
            }
        }
			
			  
	}
	
	function delete($id,$forceDelete = false, $reason = ''){
        
		$arrayToJs =  array();
		// tdk bisa didelete utk transaksi, tp ubah ke cancel
		//if($this->isTransaction || (isset( $this->tableNameDetail) && !empty($this->tableNameDetail))) {  
        if($this->isTransaction){ 
             $arrayToJs = $this->changeStatus($id, 4,$reason,false,$forceDelete);  
             //$this->setLog($arrayToJs,true);
             return $arrayToJs; 
		} 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id,$forceDelete);
			
			if (!empty($arrayToJs))  return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
                $rs = $this->getDataRowById($id);
             
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			  
                $this->deleteReference($id);
				$this->deleteFiles($id);
             
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyDeleted']); 
				 
                $this->afterCommitDelete($rs); 
            
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    function  deleteReference($id){  
        if (isset($this->arrDeleteTable)){ 
            for ($i=0;$i<count($this->arrDeleteTable);$i++){
                
                if (is_array($this->arrDeleteTable[$i]['field'])){
                     $criteria = array();
                    
                     foreach ($this->arrDeleteTable[$i]['field'] as $columnname => $value){  
                         $value = str_replace('{id}', $id,$value);
                         array_push($criteria,  $columnname.'='.$this->oDbCon->paramString($value));  
                     }
                    
                    $criteria = implode(' and ', $criteria);
                   
                }else{
                    $criteria = $this->arrDeleteTable[$i]['field'] . '=' .$id;
                }
           
                $sql = 'delete from ' .$this->arrDeleteTable[$i]['table'].' where  ' . $criteria;  
				$this->oDbCon->execute($sql);
                
            }
        } 
            
       // $this->deleteContactPerson($id); 
    }
 	
    function adjustDetailForCancelAndCopy ($tableName,&$rsDetail){}
    
    function duplicateS3Folder($arrSourcePath,$destinationPath){
         
        $client = $this->initS3Client();

        $bucket = STORAGE['bucket'];
        // DOMAIN_NAME ditembak saja, copy2 harus sebatas dalam satu domain
        
        if(is_array($arrSourcePath)){
            $sourcePrefix = DOMAIN_NAME.'/'.$arrSourcePath['sourcePath']; 
            if (isset($arrSourcePath['prefixPath']) && !empty($arrSourcePath['prefixPath'])){
                $prefixPath = $arrSourcePath['prefixPath'];
                if(substr($prefixPath,-1) <> "/")  $prefixPath  .= '/';
                    $sourcePrefix = $prefixPath.$sourcePrefix;
            }
        }else{ 
            $sourcePrefix = DOMAIN_NAME.'/'.$arrSourcePath; 
        }
        
        
        
        if(substr($sourcePrefix,-1) <> "/")  $sourcePrefix  .= '/';  // Folder to copy from (must end with /)
         
        $destinationPrefix = DOMAIN_NAME.'/'.$destinationPath;  
        if(substr($destinationPrefix,-1) <> "/")  $destinationPrefix  .= '/';  // Folder to copy from (must end with /)
 
        try {
            // List all objects in the source folder
            $objects = $client->getPaginator('ListObjectsV2', [
                'Bucket' => $bucket,
                'Prefix' => $sourcePrefix,
            ]);

            foreach ($objects as $result) {
                foreach ($result['Contents'] as $object) {
                    $sourceKey = $object['Key'];

                    // Skip the "folder" placeholder key (if any)
                    if (substr($sourceKey, -1) === '/') continue;

                    // Get the relative path from the source folder
                    $relativePath = substr($sourceKey, strlen($sourcePrefix));

                    $destinationKey = $destinationPrefix . $relativePath;
 
                    // Copy the object
                    $client->copyObject([
                        'Bucket'     => $bucket,
                        'Key'        => $destinationKey,
                        'CopySource' => "{$bucket}/{$sourceKey}",
                        'ACL'        => 'private', // Change if needed
                    ]);
 
                }
            }


        } catch (AwsException $e) {
            $this->setLog("Error: " . $e->getMessage());
        }
    }
    
    
    function updateDetailTablesOnCopyV2($id,$newPkey, $arrData, $headerkey){
        
        //if (empty($headerkey)) $headerkey = $id; // harusnya cuma masuk pertama kali
        
        // get details table
        $detailOptions = $arrData['pkey'][1]['dataDetail']; // ini bisa balikinnya array. jd standartkan saja
         // if only have 1 dimension array
        if (!isset($detailOptions[0])){ 
            $temp = $detailOptions;
            $detailOptions = array();
            array_push($detailOptions, $temp);  
        }    



        // loop semua detail cari yg tipenya file, ambil data UploadFolder 
        try{
             if($this->useStorage('S3')){ 
                $arrFolderNeedToUpload = $this->getUploadedFilesPath($id,array(),false);  
                //$this->setLog($arrFolderNeedToUpload,true);
                foreach($arrFolderNeedToUpload as $uploadFolderRow){
                    //$this->setLog($uploadFolderRow . $id.','. $uploadFolderRow . $newPkey,true);
                    $this->duplicateS3Folder($uploadFolderRow . $id, $uploadFolderRow . $newPkey); 
                }
             } 
       	} catch (Exception $e) {
            $this->setLog($e->getMessage(),true);
        }
         

        // utk setiap detail 
		
        foreach($detailOptions as $detailOption) { 
			 
            $tableName = (isset($detailOption['tableName'])) ? $detailOption['tableName'] : $this->tableNameDetail;
            $dataset = (isset($detailOption['dataset'])) ? $detailOption['dataset'] : '';
 
            $hasDetail = false;
            // kalo punya detail,
            // pkey harus query ulang
             
            $hasDetail = (isset($dataset['pkey'][1]['dataDetail'])) ? true : false; 
            
            $rsDetail = $this->getDetailById($id,'','',$tableName); // harus ganti kah ?
            //$this->setLog($rsDetail);
            
            foreach ($rsDetail as $detail){ 
                 
                $arrField = array();
                $arrField['field'] = array();
                $arrField['value'] = array();
                
                 if($hasDetail){ 
                    $newDetailPkey = $this->getNextKey($tableName);
                    $this->updateDetailTablesOnCopyV2($detail['pkey'],$newDetailPkey,$dataset, $headerkey);
                     
                    // kalo ad detail lg, pkey harus diinsert, gk boleh autoincrement
                    array_push($arrField['field'], 'pkey');
                    array_push($arrField['value'], $newDetailPkey);
                } 

                $sql = 'show columns from ' . $tableName;   
                $rsColumnsName = $this->oDbCon->doQuery ($sql); 
 
                $detail['refkey'] = $newPkey; 
                if (isset($detail['refheaderkey'])) $detail['refheaderkey'] = $headerkey;
                    
               
                for ($i=1;$i<count($rsColumnsName);$i++){
                     array_push($arrField['field'],  $rsColumnsName[$i]['Field']);
					
					// jgn pake paramstring, nanti & makin panjang &amp; nya
					array_push($arrField['value'],  '\''.addslashes($detail[$rsColumnsName[$i]['Field']]).'\''); 
					
                }

                $sql = 'insert into ' .$tableName.'  ('.implode(',',$arrField['field']).') values ('.implode(',',$arrField['value']).')'; 
                //$this->setLog($sql,true);
                $this->oDbCon->execute ($sql);	

            } 
			
            // kalo tipe S#, cari semua folder yg berhubungan dengan data ini
            if($this->useStorage('S3')){
                // pindah diatas karena gk looping disini 
            }else{
               // utk tipe file dan image masukin ke queing upload 
                if(isset($detailOption['datatype']) &&  in_array($detailOption['datatype'], array('image','file') )){

                    $uploadFolder = $detailOption['uploadFolder']; // format arrData berbeda dengan S3
                    $sourcePath = $this->defaultDocUploadPath . $uploadFolder . $id;
                    $destinationPath =  $this->defaultDocUploadPath . $uploadFolder . $newPkey;

                    if(is_dir($sourcePath))
                        $this->fullCopy($sourcePath, $destinationPath);

                }
				
		 
            }
		
        }
        
    }
    
    function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 
         
        for($k=0;$k<count($arrTableDetail);$k++){
            $rsDetail = $this->getDetailById($id,'','',$arrTableDetail[$k]);

            $this->adjustDetailForCancelAndCopy($arrTableDetail[$k], $rsDetail);
                
            $sql = 'show columns from ' . $arrTableDetail[$k] ;   
            $rsColumnsName = $this->oDbCon->doQuery ($sql); 

            for ($j=0;$j<count($rsDetail);$j++){
                $fields = '';
                $data = '';

                $rsDetail[$j]['refkey'] = $newPkey; 
                
                for ($i=1;$i<count($rsColumnsName);$i++){

                    $fields .= $rsColumnsName[$i]['Field']; 

                    //$data .=   $this->oDbCon->paramString($rsDetail[$j][$rsColumnsName[$i]['Field']]);
					
					$data .= '\''.addslashes($rsDetail[$j][$rsColumnsName[$i]['Field']]).'\'';

                    if ($i <> count($rsColumnsName) - 1){
                      $data .= ',';   
                      $fields.= ',';    
                    }

                }

                $sql = 'insert into ' .$arrTableDetail[$k].'  ('.$fields.') values ('.$data.')'; 
                $this->oDbCon->execute ($sql);	

            }  
        }  
        
    }
    
    function afterAddDataOnCopy($pkey, $oldkey){
        
        
    }
    
    function validateDelete($pkey,$autoDelete=false){
        
		$arrayToJs =  array(); 
              
        $rs = $this->getDataRowById($pkey); 
        
        if (isset($rs[0]['systemVariable']) && $rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[211]);
			return $arrayToJs;
		}  
        
        //cek ada table yg berhubungan gk ?
        // kalo ad langsugn update islinked = 1  
        //tp bagaimana misalnya contoh utk kategori kota, kota nya sudah dihapus, tp status di kategori kota masi 1 isloclednya
    
		if ( !$autoDelete ) {
			if (isset($rs[0]['islinked']) && $rs[0]['islinked'] == 1) 
				$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[900]);
        }  
		
        
        if (isset($rs[0]['islocked']) && $rs[0]['islocked'] == 1){ 
            $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[900]);
        }else if (isset($this->arrLockedTable) && !empty($this->arrLockedTable)){ 
            for($i=0;$i<count($this->arrLockedTable);$i++){
                $table = $this->arrLockedTable[$i]['table'];
                $field = $this->arrLockedTable[$i]['field'];
                
                //buat jaga2 utk beberapa DB yg gk uptodate table2nya
                $sql = 'SHOW TABLES LIKE '.$this->oDbCon->paramString($table);
                $rsTable = $this->oDbCon->doQuery($sql);
                if (empty($rsTable))
                    continue;
                    
                $sql = 'select pkey from '.$table.' where ' . $field . ' = ' . $this->oDbCon->paramString($pkey) .' limit 1';
                //$this->setLog($sql);
                
                $rsLink = $this->oDbCon->doQuery($sql);
                
                if (!empty($rsLink)){
                    
                    if ($this->oDbCon->startTrans()){ 
                        $sql = 'update '.$this->tableName.' set islocked = 1 where pkey = ' . $this->oDbCon->paramString($pkey);
                        $this->oDbCon->execute($sql); 
                        $this->oDbCon->endTrans();  
                
                        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[900]);
                        break;

                    }else {
                        $this->oDbCon->rollback(); 
                    }
 
                }
            } 
        }
        
        
 		return $arrayToJs; 
        
    }
    
	// form component
	
    
    function getNextAvailableStatus($statuskey){
         $nextstatuskey = $statuskey + 1;
        
         for($i=0;$i<3;$i++){
              $rsStatus = $this->getStatusById ($nextstatuskey);

              // buat jaga2 kalo statusnya ad yg sengaja di skip di user, misalny status pengajuan di TCO
              if(empty($rsStatus))
                  $nextstatuskey +=1;
             else
                 break;
         }

        return $rsStatus;
    }
    
	function generateSaveButton($allowedStatusForEdit = array(), $saveAndProceed = false){  
        
		$security = new Security();
          
        $options = array();
        
        if (empty($allowedStatusForEdit))
            $allowedStatusForEdit = $this->allowedStatusForEdit;
        
        // kalo ada set page
        $arrPage = (isset($_GET) && !empty($_GET['idcol'])) ? json_decode($_GET['idcol'],true) : array();
        
        $currPage = -1;
        $nextPage = -1;
        $prevPage = -1;
        
        $prevButton = '';
        $nextButton = '';
        
        if(isset($_GET) && !empty($_GET['id'])){
            $currPage = $_GET['id'];
            
            $currPageIndex = array_search($currPage,$arrPage,true);
            
            $nextPage = (($currPageIndex + 1) < count($arrPage)) ? ($currPageIndex + 1) : -1;
            $prevPage = ( $currPageIndex > 0 ) ? ($currPageIndex - 1) : -1;
            
            $prevButton = ($prevPage >= 0) ? '<div class="prev-page" attr-pkey="'.$arrPage[$prevPage].'"><i class="fad fa-arrow-alt-circle-left"></i></div>' : '<div class="prev-page-muted"><i class="fad fa-arrow-alt-circle-left"></i></div>';
            $nextButton = ($nextPage >= 0) ? '<div class="next-page" attr-pkey="'.$arrPage[$nextPage].'"><i class="fad fa-arrow-alt-circle-right"></i></div>' : '<div class="next-page-muted"><i class="fad fa-arrow-alt-circle-right"></i></div>';
        }
       
        $pagingForm = '<ul class="paging-form"><li>'.$prevButton.'</li><li>'.$nextButton.'</li></ul><div style="clear:both;"></div>';
        
        //if (!empty($allowedStatusForEdit))
            
        $options['allowedStatusForEdit'] = $allowedStatusForEdit;
		if($security->isAdminLogin($this->securityObject,11,false)) { 
            
             $rs = $this->rs;
             $allStatus = $this->getAllStatus();
             $cancelStatusKey = $allStatus[count($allStatus)-1]['pkey'];
            
             $button = '';
            
             // kalo data master, selalu bisa edit
             //if(!$this->isTransaction) 
             //    $options['allowedStatusForEdit'] = array_column($allStatus,'pkey');
            
             $saveButtonEnabled = false;
             if (!isset($rs[0]) || !$this->isTransaction){  
                 $saveButtonEnabled = true;
             }else if ($this->isTransaction && $rs[0]['statuskey'] != $cancelStatusKey){  
                 $saveButtonEnabled = true;
             }
                 
            
             // kalo data baru atau gk sama dengan batal 
             // if (!isset($rs[0]) || $rs[0]['statuskey'] != $cancelStatusKey){  
            if($saveButtonEnabled){ 
                     $button  = $this->inputSubmit('btnSave', $this->lang['save'], $options); 
                     if ($saveAndProceed){ 
                         $hasAccess = false;

                         $statuskey = (isset($rs) && !empty($rs)) ? $rs[0]['statuskey'] : 1 ;
//                         $nextstatuskey =  $statuskey + 1;
                         $rsStatus = $this->getNextAvailableStatus($statuskey);
                         $nextstatuskey = $rsStatus[0]['pkey'];
                         
                         //klao statusnya cancel   
                          // kalo next status cancel 
                      
                         if ($nextstatuskey < $cancelStatusKey && $security->isAdminLogin($this->securityObject,$nextstatuskey,false))
                             $hasAccess = true; 

                         if ($saveAndProceed && $hasAccess){ 
                             $options['etc'] = ' style="margin-left:1em" ';
                             $options['class'] = 'btn btn-primary btn-princeton-orange';
                             
//                             $rsStatus = $this->getNextAvailableStatus($statuskey);
                               
                             $nextStatus = '"'.$rsStatus[0]['status'].'"';
                                 
                             $button  .= $this->inputSubmit('btnSaveAndProceed', $this->lang['saveAndProceedTo']. ' '.$nextStatus , $options);
                             $button  .= $this->inputHidden('hidSaveAndProceed', array('value' => 0));
                         } 
                     }
                 
             }  
            
             return $button . $pagingForm;
        }
         
        return $pagingForm; 
	}
	 
    function input($name, $options = array()){
			
   /*     $this->setLog($name,true);
        $this->setLog($options,true);*/
        
        $default = array( 
                          'type' => 'text', 
                          'class' => 'form-control', 
                          'add-class' => '', 
                          'id' => '' , 
                          'value' => '' , 
                          'autocomplete' => "off", 
                          'overwritePost' => true ,
                          'readonly' => false ,
                          'disabled' => false ,
                          'allowedStatusForEdit' => $this->allowedStatusForEdit,
                          'etc' => '',
                          //'companyCategory' => '',
                        );
   
        
        $type = (isset($options['type'])) ? $options['type'] : $default['type'] ; 
        $args = (empty($options)) ? $default : array_merge($default, $options);   
          
        // kalo bkn di kategori perusahaannya, gk muncul 
        /* if (!empty($args['companyCategory'])){
            if (!in_array(PLAN_TYPE['categorykey'],$args['companyCategory'] ))
                return '';
        }*/
        
        // force disable field on certain status
        $forceDisable = '';
        $forceReadonly = '';
        $classReadonly = '';
        
        if (!empty($this->rs)){ 
            $statuskey = $this->rs[0]['statuskey'];
            
            if (!empty($args['allowedStatusForEdit']) && !in_array($statuskey, $args['allowedStatusForEdit'])) { 
                $forceDisable = 'disabled="disabled"';
                $forceReadonly = 'readonly="readonly"';
                $classReadonly = 'force-readonly';
                 
                //remove class input-date on disable
                $args['class'] = str_replace('input-date','',$args['class']);
            }
        }  
     
        //khusus readonly dan tipe datetime
        //$this->setLog($name.' => '.$type,true);
        //gk bisa, karena selalu readonly 
        /* if(isset($args['readonly']) && $args['readonly']){ 
            if($type == 'date')
                $classReadonly = 'force-readonly';
        }*/
            
        
        // add force-readonly class if needed
        $args['class'] .= ' ' .$classReadonly;
        
        $args['class'] .= ' ' .$args['add-class'];
  
        if($args['overwritePost']) { 
            //if(isset($_POST) && !empty($_POST[$name]))  
            if(isset($_POST[$name]))  
                $args['value'] = $_POST[$name]; 
        }
        //$this->setLog($name . ' = ' . $args['value']);
        
        $arrAttr = array(); 
        array_push($arrAttr, 'name="'.$name.'" ');
        array_push($arrAttr, 'class="'.$args['class'].'"');
        array_push($arrAttr, ' '.$args['etc'].' ');
         
        if (!empty($args['id']))
            array_push($arrAttr, ' id="'.$args['id'].'"');
         
        switch($type){
            case 'submit' :   
                array_push($arrAttr, 'type="'.$args['type'].'" value="'.$args['value'].'"');
                break; 
            case 'button' :  
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break; 
                
            case 'linkbutton' :  
                array_push($arrAttr, 'type="button"');
                break;
                
            case 'hidden' : 
                array_push($arrAttr, 'value="'.$args['value'].'"');
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break;
                
            case 'date' : 
                array_push($arrAttr, 'value="'.$args['value'].'"'); 
                break;
                
            case 'checkbox' : 
               // array_push($arrAttr, 'value="1"'); 
                array_push($arrAttr, 'value="'.$args['value'].'"');
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break;
                
            case 'radio' :  
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break;
                
            case 'textarea' :  
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break;
                                
            case 'select' :  
                array_push($arrAttr, 'autocomplete="'.$args['autocomplete'].'"');
                array_push($arrAttr, 'type="'.$args['type'].'"'); 
                break;
                
            default :  
                array_push($arrAttr, 'autocomplete="'.$args['autocomplete'].'"');
                array_push($arrAttr, 'value="'.$args['value'].'"');
                array_push($arrAttr, 'type="'.$args['type'].'"');
                break;
                
        }
         
        // kalo sudah diforce readonly, sudah gk perlu tambahin attribute readonly lg
        if ($forceReadonly == '' && $args['readonly']){ 
            array_push($arrAttr, ' readonly="readonly" tabindex="-1" '); 
        }
         
        if ($forceDisable == '' && $args['disabled']){ 
            array_push($arrAttr, ' disabled="disabled" tabindex="-1" '); 
        }
        
        $arrAttr = implode (' ', $arrAttr);   
        
        switch($type){
            case 'textarea' : 
                $inputResult = '<textarea ' .$arrAttr.' '.$forceReadonly.'>' .$args['value'].'</textarea>'; 
                break;
            case 'select' :  
                 
		        $inputResult = '<select '.$arrAttr.' '. $forceReadonly.' >'.chr(13);  
                    
                $arrOption = $args['options'];
                $value = (!empty($args['value'])) ? $args['value'] : 0;
                 
                foreach($arrOption as $key=>$optRow){ 

                    $optLabel = (isset($optRow['label'])) ? $optRow['label'] : $optRow;
                    
                    if (is_array($optLabel)){

                        $childKeys = array_keys($optLabel);
                        $childLength = count($childKeys);

                        $inputResult  .= '<optgroup label="'.$keys[$i].'">';

                        for($j = 0; $j < $childLength; $j++){
                            $inputResult  .= '<option value="' . $childKeys[$j] . '"';

                            if (is_array($value)) {  
                                if (in_array($childKeys[$j], $value)){
                                     $inputResult .= ' selected';  
                                }  
                            }else{
                                if($childKeys[$j] == $value) 
                                    $inputResult .= ' selected'; 
                            }


                            $inputResult .= '>' .
                            $optLabel[$childKeys[$j]] . '</option>' . chr(13);
                        }

                        $inputResult  .= '</optgroup>';

                    }else{

                            $inputResult  .=  '<option value="' . $key . '"';

                            if (is_array($value)) {   
                                if (in_array($key, $value)) 
                                     $inputResult .= ' selected';   
                            }else{  
                                if($key == $value) 
                                    $inputResult .= ' selected'; 
                            } 
                        
                            if (isset($optRow['rel'])){ 
                                foreach($optRow['rel'] as  $relkey=>$relRow)  
                                   $inputResult .= ' '.$relkey.'="'.htmlentities($relRow). '" '; // knp gk pake htmlspecialchars_decode / html_entity_decode, atau karena tanda kutip ?
                            }
                         
                            $inputResult .= '>' .
                            $optLabel. '</option>' . chr(13);
                    } 

                }  
                
                $inputResult .= '</select>';
                break; 
                
            case 'submit' :   
            case 'button' :     
                $inputResult = (empty($forceReadonly)) ? '<button  ' .$arrAttr. '>'.$args['value'].' '.$this->loadingIcon.'</button>' : ''; 
                break; 
  
            case 'linkbutton' :    
                $inputResult = (empty($forceReadonly)) ? '<button  ' .$arrAttr. '>'.$args['value'].' </button>' : '';  
                break;
/*
            case 'date' :     
                $inputResult = '<input  ' .$arrAttr. ' '.$forceReadonly.'/>';  
                return $inputResult;*/

            case 'checkbox' :     
                $checked =  (!empty($args['value'])) ? 'checked="checked"' : '';
                
                $etc = (empty($forceReadonly)) ?  '' : 'onclick="return false"';
                 
                $inputResult = '<input  ' .$arrAttr. ' ' . $checked . ' ' .$etc.' />';  
                return $inputResult;
                
            case 'radio' :    
                  
                $etc = (empty($forceReadonly)) ?  '' : 'onclick="return false"';
                  
                $inputResult = '';
                $optionItems = $args['optionItems'];
                for($i=0;$i<count($optionItems);$i++) { 
                    if ($i == 0)
                        $checked = 'checked' ;
                    else
                        $checked =  (!empty($args['value']) && $args['value'] == $optionItems[$i]['value']) ? 'checked' : '';

                    $inputResult .= '<div class="form-check form-check-inline">';
                    $inputResult .=  '<input ' .$arrAttr. ' value="'.$optionItems[$i]['value'].'" '.$checked.'><label>' . $optionItems[$i]['label'] .'</label>'; 
                    $inputResult .= '</div>';
                }
                
                return $inputResult;
                       
			case 'password' :
            	$targetID = $_SESSION['targetID']++; 
				$inputResult = '<input  ' .$arrAttr. ' '.$forceReadonly.' /><i id="'.$targetID.'" class="mnv-show-password fas fa-eye-slash" rel="-1"></i>'; 
				 
				$script = ' $("#'.$targetID.'").closest("div").find(".form-control-feedback").remove();
							$("#'.$targetID.'").click(function() { 
								   var rel = $(this).attr("rel");
								   var passObj = $(this).closest("div").find("input");

								   if(rel == -1){
									   $(this).removeClass("fa-eye-slash").addClass("fa-eye");
									   passObj.attr("type","text");
								   }else{
									   $(this).removeClass("fa-eye").addClass("fa-eye-slash");
									   passObj.attr("type","password");
								   }
                                   
								    $(this).attr("rel",rel*-1);
                                    

								});';
				
				$script = $this->minimizeJavascriptSimple($script); 
				$inputResult .= '<script type="text/javascript">if (typeof jQuery !== \'undefined\') {jQuery(document).ready(function(){  '.$script.' });} </script>';
				
					
				return $inputResult;
            default :     
                $inputResult = '<input  ' .$arrAttr. ' '.$forceReadonly.' />'; 
        } 
        
             
		// TEMP	LANG
        $useLang = (isset($args['multilang']) && $args['multilang']) ? true : false;
        if ($useLang){ 
            $lang = new Lang();
            $rsLang = $lang->searchData($lang->tableName.'.statuskey',1);
            
            $arrLang = array_column($rsLang,'pkey');
                
            unset($options['multilang']);  
            
            $inputResult = '<div rellang="'.$arrLang[0].'">'.$inputResult.'</div>'; 
            
            // LANG pertama default ,dilewatin
            for($i=1;$i<count($arrLang);$i++)
                $inputResult .= '<div style="display:none" rellang="'.$arrLang[$i].'">'.$this->input($name.'-'.$arrLang[$i], $options ).'</div>'; 
        }
        
		return $inputResult;
	}  

    function inputText($name,$options = array()){
        if(!isset($options['type']))
            $options['type'] = 'text';
        
        return $this->input($name,$options);
    }
        
    function inputPassword($name,$options = array()){
        $options['type'] = 'password';
        return $this->input($name,$options);
    }
    
    function inputButton($name, $value, $options = array()){
        $options['type'] = 'button';
        $options['value'] = $value; 
        if (empty($options['class']))
            $options['class'] = 'btn btn-primary'; 
            
        return $this->input($name,$options);
    }
    
    function inputLinkButton($name, $value, $options = array()){
        $options['type'] = 'linkbutton'; 
        $options['value'] = $value;
          if (empty($options['class']))
            $options['class'] = 'btn btn-link';
        return $this->input($name,$options);
    }
    
    function inputSubmit($name, $value, $options = array()){
        $options['type'] = 'submit';
        $options['value'] = $value;
        
          if (empty($options['class']))
            $options['class'] = 'btn btn-primary'; 
        
        return $this->input($name,$options);
    }
    
    function inputSelect($name,$optionsValue = array(), $options = array()){
        $options['type'] = 'select';
        $options['options'] = $optionsValue;
        return $this->input($name,$options);
    }
    
    function inputTextArea($name,$options = array()){
        $options['type'] = 'textarea';
        return $this->input($name,$options);
    }
    
    function inputInteger($name,$options = array()){
        $options['type'] = 'text';
        if (empty($options['class']))
            $options['class'] = 'form-control input-integer';
        
        return $this->input($name,$options);
    }
     
    function inputNumber($name,$options = array()){
        $options['type'] = 'text';
        if (empty($options['class']))
            $options['class'] = 'form-control inputnumber';
        
        return $this->input($name,$options);
    }
     
    function inputCollapsibleNumber($name,$options = array()){
        $options['type'] = 'text';
        if (empty($options['class']))
            $options['add-class'] = 'mnv-total-field readonly-enable-style';
        
        $icon = '<i class="collapsible-icon fas fa-caret-down"></i>';
        
        
        //$returnValue = (isset($options['autodecimal']) && $options['autodecimal'] == true ) ? $this->inputAutoDecimal($name,$options) : $this->inputNumber($name,$options);
        
        $returnValue = $this->inputNumber($name,$options);  //form2 biasa pakenya input number
		
		if(!empty($options['format'])){
			switch($options['format']){
				case 'decimal' :  $returnValue = $this->inputDecimal($name,$options);
								  break;
			} 
		}
        
        return $returnValue.$icon;
    }
    
    function inputDecimal($name,$options = array()){
        $options['type'] = 'text';
        if (empty($options['class']))
            $options['class'] = 'form-control inputdecimal';
        
        return $this->input($name,$options);
    }
    
    function inputAutoDecimal($name,$options = array()){
        $options['type'] = 'text';
        if (empty($options['class']))
            $options['class'] = 'form-control inputautodecimal';
        
        return $this->input($name,$options);
    }
          
    
    function inputHidden($name,$options = array()){
        $options['type'] = 'hidden';
        return $this->input($name,$options);
    }
         
    function inputCheckBox($name,$options = array()){ 
         
        $postValue = (isset($options['value'])) ? $options['value'] : '0';  /// last update tambahin default value 0
        
        $onChangeFunction = (isset($options['onChangeFunction'])) ? ','.$options['onChangeFunction'] : '';
         
        $targetID = $_SESSION['targetID']++;  
        // utk checkbox harus buat dummy  
        $options['overwritePost'] = false;
        $options['value'] = (isset($_POST[$name])) ? $_POST[$name] : $postValue; 
        $options['type'] = 'checkbox';  
        $options['class'] = ''; 
        if(!isset($options['etc'])) $options['etc'] = '';
        $options['etc'] .=  ' onClick="updateChkBoxOnClick(this'.$onChangeFunction.')" ';  
        $dummy = $this->input('dummy'.$name,$options);
         
        
        // harus inherit disablenya jg ....
        $newOptions = array();   
        $newOptions['value'] = $options['value']; 
        $newOptions['class'] = ''; 
        $newOptions['disabled'] = (isset($options['disabled'])) ? $options['disabled'] : '';
        $newOptions['etc']  =  ' onChange="updateChkBoxOnChange(this)" '; 
        $obj = $dummy . $this->inputHidden($name,$newOptions);
        
        return $obj;
    }
    
    function inputRadio($name,$options = array()){
        $options['type'] = 'radio';
        $options['class'] = 'form-check-input';
        return $this->input($name,$options); 
    }
         
    function inputAutoCode($name,$options = array()){ 
        $options['type'] = 'text';  
        $options['readonly'] = ($this->usingAutoCode) ? true : false; 
        $options['value'] = ($this->usingAutoCode) ? '[auto code]' : ''; 
        return $this->inputText($name,$options);
    }
    
    function inputDate($name,$options = array()){ 
        
        if(isset($options['readonly']) && $options['readonly'])
             return $this->inputText($name,$options);
           
        $options['type'] = 'date';  
        $options['readonly'] = true; 
        $options['class'] = 'form-control input-date';
        
         if(isset($options['allowEmpty']) && $options['allowEmpty']){ 
             if (!isset($options['etc'])) $options['etc'] = '';
             $options['etc'] .= ' attr-allow-empty="true" '; 
         }
        
        //khusus elemen2 yg terdaftar, atau trdate kalo kosong
        // kalo ad akses bypass tgl, gk perlu cek
        $security = new Security();
         if (!$security->hasSecurityAccess($this->userkey,$security->getSecurityKey('noTransactionDateLimit'),10)){
                $arrTable = array('trDate');
                if(!empty(TABLENAME_SETTINGS[$this->tableName]['datepickername'])) 
                    $arrTable = json_decode(TABLENAME_SETTINGS[$this->tableName]['datepickername']);


                if(in_array($name, $arrTable)){ 
                    $maxDays = (isset(TABLENAME_SETTINGS[$this->tableName]['datepickermaxdays'])) ?  TABLENAME_SETTINGS[$this->tableName]['datepickermaxdays'] : 0;
                    $minDays = (isset(TABLENAME_SETTINGS[$this->tableName]['datepickermindays'])) ?  TABLENAME_SETTINGS[$this->tableName]['datepickermindays'] : 0;

                    if ($maxDays > 0)  $options['etc'] .= ' max-days="'.$maxDays.'" '; 
                    if ($minDays > 0)  $options['etc'] .= ' min-days="-'.$minDays.'" ';  
                }

         }
      
        
        return $this->inputText($name,$options);
    }
        
    function inputDateTime($name,$options = array()){ 
                
        if(isset($options['readonly']) && $options['readonly'])
             return $this->inputText($name,$options);
        
        $options['type'] = 'date';  
        $options['readonly'] = true;
        $options['class'] = 'form-control input-datetime';
        
         if(isset($options['allowEmpty']) && $options['allowEmpty']){ 
             if (!isset($options['etc'])) $options['etc'] = ''; 
             $options['etc'] .= ' attr-allow-empty="true" ';
         }
             
        
        return $this->inputText($name,$options);
    }
    
    function inputMonth($name,$options = array()){ 
        if(isset($options['readonly']) && $options['readonly'])
             return $this->inputText($name,$options);
        
        $options['type'] = 'date';  
        $options['readonly'] = true;
        $options['class'] = 'form-control input-month';
        return $this->inputText($name,$options);
    }   
    
    function inputEditor($name,$options = array()){ 
     
        $targetID = $_SESSION['targetID']++;  
        $inputResult = $this->inputHidden($name); 
        $inputResult .= $this->inputTextArea($name.'-ckeditor', array('id' => $targetID, 'value' => $_POST[$name], 'class' => 'form-control ckeditor', 'etc' => 'attr-rel-val="'.$name.'"')); 
        $inputResult .= '<script>$(function(){ $("#'.$targetID.'").ckeditor() })</script>';    
         
             
        $useLang = (isset($options['multilang']) && $options['multilang']) ? true : false;
        
        if ($useLang){ 
             
            $lang = new Lang();
            $rsLang = $lang->searchData($lang->tableName.'.statuskey',1);
            
            $arrLang = array_column($rsLang,'pkey');
                
            unset($options['multilang']);  
            
            $inputResult = '<div rellang="'.$arrLang[0].'">'.$inputResult.'</div>'; 
            
            // LANG pertama default ,dilewatin
            for($i=1;$i<count($arrLang);$i++){  
            
               /* if (strpos($name, '[]') === false){  
                    $this->setLog('in',true);
                    $newName = $name.'-'.$arrLang[$i];
                }else{
                    $this->setLog('out',true);
                    $newName = str_replace('[]','',$name) ;
                    $newName .= '-'.$arrLang[$i].'[]';
                }*/
             
                   
                $newName = $name.'-'.$arrLang[$i];
                $inputResult .= '<div style="display:none" rellang="'.$arrLang[$i].'">'.$this->inputEditor($newName, $options ).'</div>'; 
            }
        }
        
        return $inputResult;
    }
    
     function inputUploadFilePlugin($rs, $rsFileDetail = array(),$opt = array()){
         
            $totalFileRows = count($rsFileDetail ?? []);  
            $elName = (!empty($opt['elName'])) ? $opt['elName'] : 'fileDetail[]';
            $elDetailPkey = (!empty($opt['elDetailPkey'])) ? $opt['elDetailPkey'] : 'hidDetailFileKey[]';
            $allowedStatusForEdit =  (!empty($opt['allowedStatusForEdit'])) ? $opt['allowedStatusForEdit'] : array(); // defaultnya meunggu sj
            $multiRow = (isset($opt['multiRow'])) ? $opt['multiRow'] : true;
                   
            $parentkey = $rs[0]['pkey'];
         
            //$return = '<div class="form-group"> 
            //                <div class="col-xs-12">   
            //                   ';
            $return .= '<div class="div-table transaction-detail file-upload-detail row-panel" style="width:100%">';
          
            for ($i=0;$i<=$totalFileRows; $i++){  

                $class =  'transaction-detail-row';
                $overwrite = true;
                $disabled = false; 

                if ($i == $totalFileRows ){

                    //tidak boleh ad row template untuk upload file yang hanya 1 baris,  masalah utk POST datanya karena bukan array
                    // tp kalo add, gk boleh continue, harus muncul
                    
                    if($multiRow){ 
                        $class = 'file-row-template row-template';
                        //$class = 'detail-row-template row-template'; // biar bisa otomatis add new row di JS
                        $overwrite = false;
                        $disabled = true; 
                    }else{
                        
                        if (empty($parentkey)){
                            // utk yg bkn multirow, dan add pertama
                            // tdk boleh continue...
                            
                        }else{
                            continue;
                        }
                        
                    }
                    
                    //if(!$multiRow) continue;

                    //$class = 'file-row-template row-template'; 
                    //$overwrite = false;
                    //$disabled = true; 
                } else {   
                    $_POST[$elDetailPkey] =  $rsFileDetail[$i]['pkey'];
                    $_POST[$elName] =  $rsFileDetail[$i]['file'];   
                } 

                 $inputParam = array('overwritePost' => $overwrite,'disabled' => $disabled);
                 if(!empty($allowedStatusForEdit))
                   $inputParam['allowedStatusForEdit'] = $allowedStatusForEdit;

                 $return .= '<div class="div-table-row '.$class.' odd-style-adjustment">
                                <div class="div-table-col detail-col-detail">';
  
                 $return .= $this->inputFile($elName,array_merge($inputParam,array('pkey' => $parentkey))); 
                 $return .= $this->inputHidden($elDetailPkey,$inputParam); 

                $return .= ' </div>'; 
                
                if ($multiRow){
                     $return .= '<div class="div-table-col detail-col-detail icon-col '.$this->hideOnDisabled($allowedStatusForEdit).'">'.$this->inputLinkButton('btnAddDetailRows' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'allowedStatusForEdit' =>$allowedStatusForEdit, 'etc' => 'attr-template="file-row-template"')).'</div>'; 
                    
                     if(isset($opt['sortable']) && $opt['sortable']){
                        $return .= '  <div class="div-table-col detail-col-detail icon-col '.$this->hideOnDisabled($allowedStatusForEdit).'">
                                        <div class="flex">
                                            '. $this->inputHidden('hidOrderList'.$elName, array('readonly' => true)) .' 
                                            <i class="fas arrow-nav fa-arrow-circle-up" rel="-1" rel-ctr="hidOrderList'.$elName.'"></i>
                                            <i class="fas arrow-nav fa-arrow-circle-down " rel="1" rel-ctr="hidOrderList'.$elName.'"></i>
                                        </div> 
                                    </div>'; 

                     }
                        
                    $return .= '<div class="div-table-col detail-col-detail icon-col '.$this->hideOnDisabled($allowedStatusForEdit).'">'.$this->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button','allowedStatusForEdit' => $allowedStatusForEdit, 'etc' => 'tabIndex="-1"')).'</div>'; 
                }
                
                $return .= '</div>'; 
                
              }  

                //$return .= ' </div>     
                //                </div>';
         
                // jika boleh revisi setelah konfirmasi dan jika bukan status menunggu
                if (!empty($allowedStatusForEdit) && $rs[0]['statuskey'] > 1 ){
                    $return .=  '<div style="margin-top:1em; margin-left: 0.3em"> 
                                    '.$this->inputButton('btnSubmitFileAjax', $this->lang['update'], array('allowedStatusForEdit' => $allowedStatusForEdit,'class' =>'btn btn-primary btn-second-tone')) .'  
                                </div>';
                }

         $return .= '</div>';
         return $return;
     }
    
    function inputFile($name, $options = array()){
        
        $options['type'] = 'file';
        
        $obj = '';
         
        if(!isset($options['overwritePost'])) $options['overwritePost'] = true;
        
        $postValue = ($options['overwritePost'] && isset($_POST[$name])) ? $_POST[$name] : '';
        
        $uploadFolder = $this->searchUploadFolderPath($name);
        
        $pkey = (!empty($options['pkey'])) ? $options['pkey'] : '';
        
        $newOptions = array();   
        $newOptions['value'] = $postValue; 
        $newOptions['overwritePost'] = false;
        $newOptions['class'] = ''; 
        $newOptions['disabled'] = (isset($options['disabled'])) ? $options['disabled'] : ''; 

		
		$obj .= '<div class="upload-panel">';
        if(!empty($postValue)){ 
            $fileUrl = $this->createPresignedURL(DOMAIN_NAME.'/'.$uploadFolder.$pkey.'/'.$postValue); 
             
            $previewImage = $postValue;
            if(in_array(strtolower(pathinfo($postValue, PATHINFO_EXTENSION)), $this->isImageExt)){
                $previewImage = '<div class="upload-image-preview" style="background-image:url('.$fileUrl.')"></div>';
            }
            
              $obj .= '
                    <div class="upload-item">
                         <ul class="file-list">
                            <li class="preview">
                                <div class="flex">
                                    <div class="consume"><a  href="'.$fileUrl.'" target="_blank">'.$previewImage.'</a></div> 
                                 </div>    
                            </li></ul>
                    </div>
                 '; 
        }else{

        }
		 
		
        $obj .= $this->inputHidden('hid'.$name,array('value' => $postValue),$options);
        $obj .= $this->input($name,$options);
		$obj .= '</div>';
        return $obj; 
        
    }
    
    
    //function createPresignedUploadUrl($fileName, $timeLimit='+2 minutes',$client='', $opt = array()) {
    // blm kepake, karena ad hal tertentu dipindahkan ke api/v3
    //    
    //    if (empty($fileName)) return '';
    //    
    //    // filename harus dipaksa sesuai domain
    //    $fileName = DOMAIN_NAME . '/'.$fileName;
    //     
    //    $uploadSize = (isset($opt['uploadSize'])) ? $opt['uploadSize'] : (10 * 1024 * 1024);// 10MB max
    //        
    //    try { 
    //       
    //        if(empty($client)) $client = $this->initS3Client();
    //        $fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8'); 
//
    //        // Create command for uploading
    //    
    //        $cmd = $client->getCommand('PutObject', [
    //            'Bucket' => STORAGE['bucket'],
    //            'Key'    => $fileName,
    //            'Conditions' => [
    //                ["starts-with", "$Content-Type", "image/"],   // allow all images
    //                ["eq", "$Content-Type", "application/pdf"],   // allow PDF
    //                ["content-length-range", 1, $uploadSize] // 10MB max
    //            ],
    //        ]);
    //   
//
    //        // Create presigned URL
    //        $request = $client->createPresignedRequest($cmd,$timeLimit);
//
    //        return (string) $request->getUri();
 //
    //    } catch (AwsException $e) {
    //       $this->setLog($e->getMessage(),true);
    //    }
    //    
    //    
    //}

    
    
     function createPresignedURL($fileName,$timeLimit='+30 minutes',$client=''){
        
        // filename hanya path harusnya tanpa ada domain
         
        if (empty($fileName)) return '';
         
        try { 
           
            if(empty($client)) $client = $this->initS3Client(); 
            $fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8'); 
            
            try {
                // Check if object exists
                $client->headObject([
                    'Bucket' => STORAGE['bucket'],
                    'Key'    => $fileName,
                ]);
            } catch (\Aws\S3\Exception\S3Exception $e) {
                // If object does not exist (404)
                if ($e->getStatusCode() === 404) {
                    return false;
                }
                // Re-throw other errors (permissions, etc.)
                //throw $e; gk usah pake dulu
            }
            
            $cmd = $client->getCommand('GetObject', [
                'Bucket' => STORAGE['bucket'],
                'Key'    => $fileName
            ]);


            // Create a pre-signed request with 10 seconds expiry
            $request = $client->createPresignedRequest($cmd, $timeLimit);

            // Get the actual pre-signed URL
            $presignedUrl = (string) $request->getUri();
            
            return $presignedUrl ;
             
        } catch (AwsException $e) {
           $this->setLog($e->getMessage(),true);
        }
    }
     
       
    function searchUploadFolderPath($name, $arrData = '', &$ctr = 0){
        // init
         
        if ($ctr > 50 ) return; // buat jaga2 aj, agak besar, karena kaya item, bisa byk  nestednya
          
        // hanya boelh sekali
        // karena ad struktur data yang level kedua, array dataDetailnya bkn  2 dimensi
        if($ctr == 0 && $arrData == '') $arrData = $this->arrData;
       
        $ctr++;
        
        foreach($arrData as $dataRow){
             
            if(isset($dataRow[1]['dataDetail'])){
                
                foreach($dataRow[1]['dataDetail'] as $detailRow){ 
                 
                  $newDataSet = (is_array($detailRow) && isset($detailRow['dataset'])) ?  $detailRow['dataset'] : '';
                  $uploadFolder = $this->searchUploadFolderPath( $name, $newDataSet ,$ctr);
                  
                  // kalo ketemu baru return, kalo gk ketemu, loop ke detail selanjutnya
                  if(!empty($uploadFolder)) return $uploadFolder;
                }
            }else{ 
                   
                if($dataRow[0] == str_replace('[]','',$name)) {   
                        return  (isset($dataRow[1]['uploadFolder'])) ? $dataRow[1]['uploadFolder'] : $this->uploadFolder; 
                }
            } 
        }
          
        return '';  
        
    }
    
    
     
    
     function inputAutoComplete($options = array()){
            $security = new Security(); 
         
            $date = new DateTime(); 
            $targetID = $_SESSION['targetID']++; 
         
            $default = array( 
                              'typeVar' => 'text', 
                              'class' => 'form-control', 
                              'value' => '' , 
                              'attr' => 'autocomplete="off"', 
                              'limit' => 25,
                              'overwritePost' => true, 
                              'revalidateField' => false,
                              'popupForm' => '',
                              'readonly' => false, 
                              'allowedStatusForEdit' => $this->allowedStatusForEdit,
                              'objRefer' => $this,
                              'placeholder' => $this->lang['pleasestarttyping'].'...',
                              'explodeScript' => false, 
							  'clearOnNotFound' => true,
                              'etc' => '',
                            );
        
            $args = array_merge($default, $options);   

            if (!empty($this->rs)){ 
                $statuskey = $this->rs[0]['statuskey'];  
                if (!empty($args['allowedStatusForEdit']) && !in_array($statuskey, $args['allowedStatusForEdit'])) 
                  $args['readonly'] = true; 
            } 


            // kalo readonly, langsung return inputtext aj 
            // jgn pake ini, nanti gk bisa on off di readonly nya kalo pake js
   /*         if ($args['readonly'])
                return $this->inputText($args['element']['value'], array ('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'readonly' => true)) . 
                       $this->inputHidden($args['element']['key']); */
           
                 
            $args['revalidateField'] = ($args['revalidateField']) ? 'true' : 'false';
            if(isset($args['readonly']) && $args['readonly']) 
                $args['quickAdd'] = false;
            else
                $args['quickAdd'] = (isset($args['popupForm']['url']) && !empty($args['popupForm']['url'])) ? true:false;
                
         
            if (isset($args['popupForm']) && !empty($args['popupForm'])){ 
                $args['popupForm']['targetID'] = $targetID;
                $args['popupForm']['revalidateField'] = $args['revalidateField'] ;  
            }
         
            $args['popupSettings'] = json_encode($args['popupForm']); 
            $args['callbackFunction'] = (isset($args['callbackFunction']) && !empty($args['callbackFunction'])) ? $args['callbackFunction'] : '';
         
            if (empty($args['callbackFunction']) && $args['quickAdd']) 
                $args['callbackFunction'] = 'quickAddCallback()'; 
         
            
			if($args['overwritePost'])  
				if(isset($_POST) && !empty($_POST[$args['element']['value']]))  
					$args['value'] = $_POST[$args['element']['value']];
			  
            $arrAttr = array();
            array_push($arrAttr, $args['attr']);
            array_push($arrAttr, 'name="'.$args['element']['value'].'"');
            array_push($arrAttr, 'type="'.$args['typeVar'].'"');
            array_push($arrAttr, 'class="'.$args['class'].'"');
            array_push($arrAttr, 'value="'.$args['value'].'"');
            array_push($arrAttr, 'placeholder="'.$args['placeholder'].'"');
        
            if ($args['readonly'])
                array_push($arrAttr, ' readonly="readonly" tabindex="-1" '); 
         
/*            if ($args['disabled'])
                array_push($arrAttr, ' disabled="disabled" tabindex="-1" '); */
         
         
            $inputResult = implode (' ', $arrAttr);   
            
            if ($args['etc'])
                $inputResult .= ' ' .$args['etc'];
         
            //$this->setLog($inputResult,true); 
            $inputResult = '<input ' .$inputResult. '/>';

            if (isset($args['element']['key']) && !empty([$args['element']['key']]))
                $inputResult .=  $this->inputHidden($args['element']['key']);
 
            $quickAddScript = '';
            $quickAddButton = '';
            
              if (isset($args['objRefer']->securityObject)){ 
                    $hasAccess =  $security->isAdminLogin($args['objRefer']->securityObject,11);  
                    if ($args['quickAdd'] && $hasAccess){  
                        $quickAddButton = '<li class="btn add-button flex-item"><i class="fas fa-file-alt"></i></li>';
                        $quickAddScript = '<script>$(function() {$("#'.$targetID.' .add-button").click(function(){loadOverlayScreen('.$args['popupSettings'].');}).closest("div").addClass("add-button-adjustment")})</script>';
                    } 
                }
          
            $inputResult = '<ul id="'.$targetID.'" class="auto-complete flex-container"><li class="textbox flex-item">'.$inputResult.'<i class="fab fa-sistrix"></i></li>'.$quickAddButton.'</ul>';      
            
           
             // kalo dr ajax
            if(isset($args['source']['url']) & !empty($args['source']['url'])){ 
                

                $ajaxParam = array();
                $data = $args['source']['data'];
                $datakeys = array_keys($data);
                for($i=0;$i<count($datakeys);$i++) 
                    array_push($ajaxParam, $datakeys[$i].'='.$data[$datakeys[$i]]);

                if ($args['limit'] > 0)
                    array_push($ajaxParam, 'limit='.$args['limit']);

                $data = implode('&',$ajaxParam);
                $data = (!empty($data)) ? '?'. $data : '';

                // CALLBACK GK BOLEH CARI BERDASARKAN NAMA, PROBLEM KALO AD NAMA YG SAMA, KECUALI UTK BARCDODE / KODE 
         
                $valueDisplayed = (isset($args['element']['displayValue']) && !empty($args['element']['displayValue'])) ? 'ui.item.' .$args['element']['displayValue'] : 'ui.item.value'; 
                //source: "'.$args['source']['url'].$data.'",
       
				$clearScript = ($args['clearOnNotFound']) ? 'clearAutoCompleteInput(this,$("#'.$targetID.' [name=\''.$args['element']['key'].'\']" ),'.$args['revalidateField'].');' : 'clearAutoCompleteKey($("#'.$targetID.' [name=\''.$args['element']['key'].'\']" ));';
                $script = '
                <script type="text/javascript"> 
                $(function() {  
                    $( "#'.$targetID.' [name=\''.$args['element']['value'].'\']" ).autocomplete({
                           source: function( request, response ) { 
                                   $.getJSON("'.$args['source']['url'].$data.'", request, response); 
                                },
                          minLength: 1,
                          autoFocus: true,
                          async : false,
                          select: function( event, ui ) {      
                                $("#'.$targetID.' [name=\''.$args['element']['key'].'\']" ).val(ui.item.pkey); 
                          },   
                          change: function( event, ui ) {   
                                 if (ui.item == null) { 
								 	'.$clearScript.'  
                                 } else {
                                     event.preventDefault();
                                     $("#'.$targetID.' [name=\''.$args['element']['value'].'\']" ).val('.$valueDisplayed.');  
                                }

                                 '.$args['callbackFunction'].'  
                            },
                      }).change(function() {    
                            if ($(this).val() == "") { 
                               	'.$clearScript.'   
							}

                      }); 
                });
                </script>';
            }else{
                $script = '
                <script type="text/javascript"> 
                $(function() { 
                    $( "#'.$targetID.' [name=\''.$args['element']['value'].'\']" ).autocomplete({
                          source: '.json_encode($args['source']).',
                          minLength: 1,
                          autoFocus: true,
                          async : false, 
                          change: function( event, ui ) {   
                                 if (ui.item == null) { 
                                  '.$clearScript.'
								};   
                            },
                      }).change(function() {    
                            if ($(this).val() == "") {
								'.$clearScript.'  
							} 
                      }); 
                });
                </script>';
            }
         
            $script = $this->minimizeJavascriptSimple($script); 
         
            $tempQuickAddScript = '<script type="text/javascript">$(function() { $( "#'.$targetID.' [name=\''.$args['element']['key'].'\']" ).change(function(){'.$args['callbackFunction'].'}); })</script>';
            
            if($args['explodeScript']){
                $arrTemp = array();
                $arrTemp['input'] = $inputResult;
                $arrTemp['script'] = $script .$quickAddScript. $tempQuickAddScript; 
                
                $inputResult = $arrTemp;
            }else{
                
                $inputResult .= $quickAddScript;
                $inputResult .= $script;
                           
                // untuk handle callback dr quickadd
                if ($args['quickAdd'])
                    $inputResult .= $tempQuickAddScript;
          
            }
         
             

            return $inputResult;
	}
    
    function minimizeJavascriptSimple($input){
        //return preg_replace(array("/\s+\n/","/\n\s+/","/ +/"),array("",""," "),$javascript);
        
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
        $input);
    }
    
 
     
	function isValueExisted($id,$columnname,$value,$exceptionStatus = ''){
            
         
        $sql = 'select pkey from '.$this->tableName.' where '.$columnname.' = '.$this->oDbCon->paramString($value);
        
		if (!empty($id))
            $sql .= '  and pkey <> ' . $this->oDbCon->paramString($id);
 
        if (!empty($exceptionStatus))
            $sql .= ' and statuskey not in ('.$exceptionStatus.')';
        
        $sql .= $this->getCreatedByCriteria();
        
        $rs = $this->oDbCon->doQuery ($sql);
	
		return $rs; 
	      
	}
	 
   
   function loadSetting($code) {   
	   // cari di preload 

	   // kalo gk isset saja, load manual dari database (utk back compability file2 yg berdiri sendiri)
	   if (defined('SYSTEM_SETTINGS')){  
           if(is_array($code)){
               
                $returnArr = array();
                foreach($code as $row) 
                   $returnArr[$row] = (isset(SYSTEM_SETTINGS[$row])) ? SYSTEM_SETTINGS[$row] : '' ;   
            
                return $returnArr;
           }else{
                return (isset(SYSTEM_SETTINGS[$code])) ? SYSTEM_SETTINGS[$code] : '' ;   
           }
	   // kedepanny kita hapus
	   }else{
		      try {
                  
                  if(is_array($code)){
                    $sql = 'select code,value from _user_setting,_setting where _setting.pkey = _user_setting.settingkey and _setting.code in ('.$this->oDbCon->paramString($code,',') .') ' ; 
                    $rs = $this->oDbCon->doQuery ($sql);
                      
                    $rs = array_column($rs,'value','code'); 
                    return $rs;

                  }else{
                        $sql = 'select value from _user_setting,_setting where _setting.pkey = _user_setting.settingkey and _setting.code = ' . $this->oDbCon->paramString($code)  ; 
                        $rs = $this->oDbCon->doQuery ($sql);

                        return (!empty($rs)) ? $rs[0]['value'] : '' ;    
                  }
				
			   }catch(Exception $e){
					 return '';
			   }	
	   }
		
	}
	
   
    function isMultiCompany($tableName = ''){
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
     
		$sql = 'select multicompany from  _code, _user_code where code ='.$this->oDbCon->paramString($tableName) .' and _code.pkey = _user_code.codekey';
		$rs = $this->oDbCon->doQuery($sql); 
	 
		return (isset($rs[0]['multicompany']) && $rs[0]['multicompany'] == 1) ? true : false ;
	}
    
	function getNextKey($tableName) {  
        
         $fpkey = 1;
		
        // get next key 
		$query = 'select nextkey from _nextkey where table_name = '. $this->oDbCon->paramString($tableName);		
		$rs = $this->oDbCon->doQuery($query);
		
		if (!empty($rs))  
		 $fpkey =  $rs[0]['nextkey']; 
        
        // always recheck last pkey
        $sql = 'select pkey from ' .  $tableName  .' order by pkey desc limit 1' ;  
        $rsNextKey = $this->oDbCon->doQuery($sql);

        if (empty($rsNextKey))
            $pkeyTable = 8000;
        else
            $pkeyTable = $rsNextKey[0]['pkey']; 
        
        if ($fpkey <= $pkeyTable) 
            $fpkey = $pkeyTable + 1;  
        
        
		if (!empty($rs))
            $query = 'update _nextkey set nextkey = '. $this->oDbCon->paramString(($fpkey + 1)).' where table_name = '. $this->oDbCon->paramString($tableName);		
	    else
            $query = 'insert into _nextkey (table_name, nextkey) values  ('.$this->oDbCon->paramString($tableName).','.$this->oDbCon->paramString(($fpkey + 1)).') ';		
	         
		//echo $query;
        $this->oDbCon->execute($query);
		  
		return $fpkey;
	}
	
	
    function getNewCustomCode($arrParam, $categoryModuleKey = '', $tableName = ''){
        // kalo categorymodulekey koosng, harus coba search 
        // kal oad berarti pake custome code
        // kalo gk ad berarti gk pake harusnya ??
        
        $customCode = new CustomCode();
          
        if (!isset($arrParam['code'])) 
            return ''; 
          
        // utk import dr template biasanya 
        if (isset($arrParam['overwriteCode']) && $arrParam['overwriteCode'])
            return $arrParam['code'];
         
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        // cek, table terdaftar gk di customcode gk...
        $rsKey = $this->getTableKeyAndObj($tableName);
        $tablekey = $rsKey['key']; 
         
        
        if(!empty($categoryModuleKey)){  // jika lebih dr satu jenis custom code
            $rsCustomCode = $customCode->searchData($customCode->tableName.'.pkey',$categoryModuleKey, true);  
        }else{ 
            $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'], true,'','order by pkey asc', 'limit 1'); 
            $categoryModuleKey =  (!empty($rsCustomCode)) ? $rsCustomCode[0]['pkey'] : 0;
        }
        $useCustomCode = (!empty($rsCustomCode)) ? true : false;
            
        
        /*$rsCustomCodeCategory = $customCode->searchData($customCode->tableCategory.'.reftabletype',$rsKey['key'], true,'','order by pkey asc', 'limit 1'); 
        
        $useCustomCode = false; 
        if(!empty($rsCustomCodeCategory)){
            $useCustomCode = true;
            
            if(empty($categoryModuleKey))
                $categoryModuleKey = $rsCustomCodeCategory[0]['pkey'];
            
            $rsCustomCode = $customCode->searchData($customCode->tableName.'.pkey',$categoryModuleKey, true); 
        }*/
         
             
        // EDIT DATA =================
        if (!empty($arrParam['hidId'])){
             
            $generateNewCode = false;  
            
            $rs = $this->getDataRowById($arrParam['hidId']); 
            
            // dikomentarin, karena mau coba kalo gk ad kolom customcodekey masi bisa pake custom code kalo terdaftar
            /*if(!isset($rs[0]['customcodekey']))
                $useCustomCode = false;*/
              
            // kalo gk pake custom code, langsung return code yg sama
            // karena gk perlu cek ulang running number
            if ( !$useCustomCode )
                return $arrParam['code'];

            // cek jenis invoice masi sama gk  
            // misalnya invoice penjualan diganti ke invoice reimburse, artinya pasti beda format kode / running number
            //$this->setLog($rs[0]['customcodekey'] .'<>'. $categoryModuleKey);
            if (isset($rs[0]['customcodekey']) && $rs[0]['customcodekey'] <> $categoryModuleKey)
                $generateNewCode = true;

            // new date
            $D = $this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),'d'); 
            $M = $this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),'m'); 
            $Y = $this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),'Y');

            // old date
            $oldD = $this->formatDBDate($rs[0]['trdate'],'d');
            $oldM = $this->formatDBDate($rs[0]['trdate'],'m'); 
            $oldY = $this->formatDBDate($rs[0]['trdate'],'Y');

/*            $this->setLog($rsCustomCode[0]['resettypekey']);
            $this->setLog($oldM .'=>'.$M);*/
            
            if ( $rsCustomCode[0]['resettypekey'] == 2){ 

                // cek bulan dan tahun masi sama gk
                if ($D <> $oldD || $M <> $oldM || $Y <> $oldY)
                    $generateNewCode = true;

            }else if ( $rsCustomCode[0]['resettypekey'] == 3){ 

                // cek bulan dan tahun masi sama gk
                if ($M <> $oldM || $Y <> $oldY)
                    $generateNewCode = true;

            }else if ( $rsCustomCode[0]['resettypekey'] == 4){ 

                // cek tahun masi sama gk
                if ($Y <> $oldY)
                    $generateNewCode = true;

            }
            
            // kalo beda gudang
            if ($rsCustomCode[0]['resetwarehouse'] == 1){

                // sementara asumsi semua fieldna selWarehouseKey
                // mungkin ad isssue utk tranfer gudang
                $warehouseKey = (isset($arrParam['selWarehouseKey'])) ? $arrParam['selWarehouseKey'] : 0;
                $oldYWarehouseKey = $rs[0]['warehousekey'];
                
                if ($warehouseKey <> $oldYWarehouseKey)
                 $generateNewCode = true;
            }

            // kalo autocode, kalo gk autocode tetep harus bisa revisi sendiri
            if($rsCustomCode[0]['useautocode'] == 1 && isset($rs[0]['codectr']) && !empty($rs[0]['codectr']))
                $arrParam['code'] = $rs[0]['codectr'];
            
            $returnCode = ($generateNewCode) ? $this->generateCustomCode($arrParam,$categoryModuleKey) : $arrParam['code']; 
            
        } else{
            // ADD DATA =================
            // cek pake custom code ap gk ...    
            $returnCode = ($useCustomCode) ? $this->generateCustomCode($arrParam,$categoryModuleKey) :  $this->getNewCode($arrParam['code']); 
             
        }   
         
        
        // EXT CODE
        $arrReturnCode = array();
        $arrReturnCode[1] = $returnCode;
        
        if(!empty($rsCustomCode[0]['extcode'])){ 
           $extCode = json_decode($rsCustomCode[0]['extcode'],true);
           $temp = '';
           foreach($extCode as $subcode){ 
               if(!is_array($subcode)){ 
                     
                   if(strtolower($subcode) == '{{code}}') 
                    $subcode = $returnCode;
                   else if ( $subcode == '{{paramPrefixCode}}')
                     $subcode = $arrParam['paramPrefixCode'];
                    
                   $temp .= $subcode;
               }else{
                   
                    $paramValue = $arrParam[$subcode[0]];
                   
                    if (!is_array($subcode[1])){
                        // kalo parameter kedua bukan array, berarti ambil kode dari table di database
                        // kalo ambil kode dr database
                        $sql = 'select code from '. $subcode[1] .' where pkey = ' . $this->oDbCon->paramString( $paramValue );
                        $rsCode =  $this->oDbCon->doQuery($sql);
                        $temp .= $rsCode[0]['code'];
                    } else{
                        // kalo ambil dr dataset / json 
                        $temp .= $subcode[1][$paramValue]; 
                    }
                   
               } 
           }    
            
           $returnCode = $temp;
        }
        
        $arrReturnCode[0] = $returnCode; 
        return $arrReturnCode ;
    }
      
    
	function getNewCode($defaultCode = '', $tableName = ''){
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        $usecode = $this->useAutoCode($tableName);  
        if($usecode <> 1)
            return $defaultCode;
        
        
		$theCode = '0';
		$counterValue = '';
		$sql = 'select code,prefix,counter,digit from  _code, _user_code where code ='.$this->oDbCon->paramString($tableName) .' and _code.pkey = _user_code.codekey';
		$rs=$this->oDbCon->doQuery($sql);
		if(count($rs)!=0){
			$format = '%s%0' . $rs[0]['digit'] . 'd';
			$theCode = sprintf($format, $rs[0]['prefix'], $rs[0]['counter']);
		}
		$sql = 'update _user_code, _code set counter='.$this->oDbCon->paramString(($rs[0]['counter'] + 1)).'
			where code ='.$this->oDbCon->paramString($tableName) .' and _code.pkey = _user_code.codekey';
		$this->oDbCon->execute($sql);  
	 
		return $theCode;
	}
	
	  
	function generateCustomCode($arrParam, $categoryModuleKey){
        
        // extcode : untuk penambahan prefix, misalnya kode gudang, tanpa mempengaruhi nomor berjalan
        // refcodetabletype : jika ada {{ REF }}, pengkodean mengacu kepada penomoran transaksi lain, misalnya inv reimburse, atau SPK mengikuti kode JO
        // codequery : buat narik code berdasarkan transaks => {{REF}}
        
      
        $customCode = new CustomCode();
        
		$theCode = '';
        $patterns = array(); 
		$replacement = array();
        
        // sementara asumsi semua fieldna selWarehouseKey
        // mungkin ad isssue utk tranfer gudang
        $warehouseKey = (isset($arrParam['selWarehouseKey'])) ? $arrParam['selWarehouseKey'] : 0;
        
		$sql = 'select * from  '.$this->tableCustomCode.' where pkey ='.$this->oDbCon->paramString($categoryModuleKey);
        $rs = $this->oDbCon->doQuery($sql);
		
		// kalo kode nya mau disambungkan dengan custom lain
		// asumsi semua format,termasuk prefix sama semua
		// utk format beda, no lanjut nanti baru diupdate lg
		if(!empty($rs[0]['refparentkey'])){ 
			$sql = 'select * from  '.$this->tableCustomCode.' where pkey ='.$this->oDbCon->paramString($rs[0]['refparentkey']);
			$rs = $this->oDbCon->doQuery($sql);
			$categoryModuleKey = $rs[0]['pkey'];
		}
		
        
        if(empty($rs))  return $theCode;
         
        // klao gk pake autocode, return kode nya
        if($rs[0]['useautocode'] == 0)  return $arrParam['code'];
        
        //cek jenis counter, ambil counter sesuai jenisnya 
        $arrFormat = array();      
         
        $codeFormat = $rs[0]['codeformat'];
        
        $arr = array();
        $arr['pkey'] = $categoryModuleKey;
        $arr['selResetType'] = $rs[0]['resettypekey'];
        $arr['selWarehouseKey'] =  ($rs[0]['resetwarehouse'] == 1) ?  $warehouseKey : 0;
         
        
        if (isset($arrParam['trDate'])){ 
            $arrFormatType = array('m','Y','y','d','mr');
            foreach($arrFormatType as $format){  
                if($format == 'mr'){
                    $arrFormat[$format] = $this->numberToRoman($this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),'m'));    
                }else{ 
                    $arrFormat[$format] = $this->formatDBDate(str_replace('\'','',$this->oDbCon->paramDate($arrParam['trDate'],' / ')),$format); 
                }
            }
            
            
            $arr['trDate'] = $arrParam['trDate']; 
            switch ($arr['selResetType']){
                case 3: $arr['trDate'] = $this->oDbCon->paramDate($arr['trDate'],' / ', '01 / m / Y');
                        break;
                case 4: $arr['trDate'] = $this->oDbCon->paramDate($arr['trDate'],' / ', '01 / 01 / Y');
                        break;
            }

            $arr['trDate'] = str_replace('\'','',$arr['trDate']); 
        }
        
       // $this->setLog($arr['trDate']);
        /*
        // pake gk pake, jadinya tetep keupdate counternya, 
        // contoh CJ, SPK nya coba cek
        // nanti kedepan baru benerin agar lebih efisien
        $counterValue = $customCode->updateCounter($arr); 
        $format = '%s%0' . $rs[0]['digit'] . 'd';
        $counterFormated = sprintf($format,'', $counterValue);  */
        
        
        $numberingFormat = '%s%0' . $rs[0]['digit'] . 'd';
        
        // query manual, kasta nya paling tinggi
        
        // {{REF}}
        // kalo ad reftabltype
        // artinya ctr nya harus berulang mengikuti Ref nya, bisa pake counter aj kah ? tp kalo ad no yg lompat gk bisa revisi
        
        $hasRefCode = (strpos($codeFormat, '{{REF}}') !== false) ? true : false; 
        
        if($hasRefCode &&  !empty($rs[0]['codequery'])){
            // kalo ad query manual 
             
            $arrQuery = json_decode($rs[0]['codequery'],true);
            
            // coba ubah di MVNET
            // LOGOL nanti coba ikutin
            
            /*$reftablekey = (isset($arrParam[$this->refAutoCode['tablekey']])) ? $arrParam[$this->refAutoCode['tablekey']] : '0';
            $refkey = (isset($arrParam[$this->refAutoCode['paramPkey']])) ? $arrParam[$this->refAutoCode['paramPkey']] : '0';
            $refObj = $this->getTableNameAndObjById($reftablekey)['obj']; 
            
            $query = $arrQuery[$reftablekey];*/
            
            // sementara 1 dimensi dulu
            $arrKeys = array_keys($arrQuery);
            $reftablekey = $arrKeys[0];
            $refObj = $this->getTableNameAndObjById($reftablekey)['obj']; 
            $refkey = $arrParam[$arrQuery[$reftablekey]['paramPkey']];
            $query = $arrQuery[$reftablekey]['query'];
            
            if(!empty($query)){
                $query .= ' and '.$refObj->tableName.'.pkey = ' . $this->oDbCon->paramString($refkey);
                $rsRef = $this->oDbCon->doQuery($query);
                
                array_push($patterns, '/({{REF}})/'); 
                array_push($replacement, $rsRef[0]['code']);
                
                $arr['refCode'] = $rsRef[0]['code'];
            }
                
        }else{ 
            if ($hasRefCode && isset ($this->refAutoCode)){ 

                $refkey = $arrParam[$this->refAutoCode['param']];

                $refObj = $this->getTableNameAndObjById($rs[0]['refcodetabletype'])['obj'];
                $rsRef = $refObj->getDataRowById($refkey);

                // kedepannya jgn ad yg disetting di class lg, jgn hardcode utk refAutoCode nya
                $rsCount = $this->searchData($this->tableName.'.'.$this->refAutoCode['refField'],$refkey); //<== bisa jadi pkey
                $countRef = count($rsCount) + 1;

                array_push($patterns, '/({{REF}})/'); 
                array_push($replacement, $rsRef[0]['code']);  

                array_push($patterns, '/({{NO}})/'); 
                array_push($replacement, sprintf($numberingFormat,'', $countRef) );

            }
        }
        
        $hasWarehouseCode = (strpos($codeFormat, '{{WRH}}') !== false) ? true : false;
        if ($hasWarehouseCode){  
            // cek field warehousekey 
            if(!empty($warehouseKey)){
                $warehouse = new Warehouse();
                $rsWarehouse = $warehouse->getDataRowById($warehouseKey);
                array_push($patterns, '/({{WRH}})/'); 
                array_push($replacement, $rsWarehouse[0]['code']);  
            }
        }
        
        // kalo sudah dipush diatas, jgn push lg
        if (!in_array('/({{NO}})/', $patterns)){
            $counterValue = $customCode->updateCounter($arr); 
            $counterFormated = sprintf($numberingFormat,'', $counterValue); 
            array_push($patterns, '/({{NO}})/'); 
            array_push($replacement, $counterFormated);  
        }
         
        foreach($arrFormat as $key=>$value){
            array_push($patterns, '/({{'.$key.'}})/');  
            array_push($replacement, $value);   
        }
        
		$theCode = preg_replace($patterns, $replacement, $codeFormat);  
           
        
        return $theCode;
       
	}
	
	  
	
	function getDataRowById($id,$searchCriteria='',$orderCriteria=''){
	     
		$sql = '
					SELECT
						'.$this->tableName .'.*
					FROM	
						'.$this->tableName .'
					WHERE	
						'.$this->tableName .'.pkey = '.$this->oDbCon->paramString($id) 
			;

		
		if (!empty($orderCriteria)){
				$sql .= ' order by ' . $orderCriteria ;
		}
		   
		return $this->oDbCon->doQuery($sql);		
			
	} 
	 
	
    function getDetailById($id,$searchCriteria='',$orderCriteria='',$tableName = ''){
        
        if (empty($tableName))
            $tableName = $this->tableNameDetail;
        
		$sql = 'select * from '. $tableName .' where refkey = ' .$this->oDbCon->paramString($id) ; 
		
        $sql  .= $searchCriteria;
        
        if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria; 
	  		  
		return $this->oDbCon->doQuery($sql);
        
	} 
    
    
	function getDetailByColumn($fieldname,$searchkey, $mustmatch=true,$searchCriteria='',$orderCriteria='', $limit=''){
		$criteria = '';
		 
		if(!empty($fieldname)){ 
			$criteria .= ' and ' ;
			
			if($mustmatch)
				$criteria .=  $fieldname .' in ('. $this->oDbCon->paramString($searchkey,',').')';
			else
				$criteria .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
		
		if($searchCriteria <> '')
			$criteria .= ' ' .$searchCriteria;
		 
		 $sql = 'select * from '.$this->tableNameDetail.'  where 1=1 ';
		 $sql  .= $criteria;
		 
		 if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria; 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit; 
			    
		return $this->oDbCon->doQuery($sql);	
		 
	} 
  
    function searchDataRow($field = array(), $criteria = '',$order = ''){
    
        // gk boleh ada tambahan getWarehouseCriteria dsb, karena kalo user mau narik data utk validasi, bisa gk muncul
        
        $fieldName = (empty($field)) ? '*' : implode(',',$field);
        
        $sql = 'select ' . $fieldName. '  from '.$this->tableName.' where 1=1 ' ;
        
        if(!empty($criteria))
            $sql .=  ' '.$criteria;
        
        if(!empty($order))
            $sql .=  ' '.$order;
        
        return $this->oDbCon->doQuery($sql);	
        
    }
    
	function searchData($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='',$orderCriteria='', $limit=''){
		
		$criteria = '';
		 
		if(!empty($fieldname)){
			
			$criteria .= ' and ' ;
			
			if($mustmatch)
				$criteria .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$criteria .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$criteria .= ' ' .$searchCriteria;
	
		$this->setCriteria($criteria); 
		$sql = $this->getQuery();
		
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria; 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit; 
             
		return $this->oDbCon->doQuery($sql);	
	} 
	
	
 	function deleteAll($directory, $empty = false) {
        //$this->setLog('directory : ' . $directory);
        
		if (empty($directory))
			return;
			
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}
	
		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
		   
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
				   
                    //$this->setLog($path);
					if(is_dir($path)) {
						$this->deleteAll($path);
					} else {
                        //$this->setLog('unlink : '.$path,true); 
						unlink($path);  
					}
				}
			}
		   
			closedir($directoryHandle);
	
			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
		   
			return true;
		}
	} 
	
	function prepareLoadedFile($id,$opt = array()){
		// utk load form 
		
        if(empty($id) || !is_numeric($id)) return;
        
		$rsItemFile = (!isset($opt['file'])) ? $this->getFileDetail($id) : $opt['file'];
		$uploadFileFolder =  (!isset($opt['uploadFileFolder'])) ? $this->uploadFileFolder : $opt['uploadFileFolder'];
		
		if (count($rsItemFile) > 0) {
			$sourcePath = $this->defaultDocUploadPath . $uploadFileFolder . $id;
			$destinationPath = $this->uploadTempDoc . $uploadFileFolder . $id;

			$this->deleteAll($destinationPath);

			if (!is_dir($destinationPath)) mkdir($destinationPath,  0755, true);

			$this->fullCopy($sourcePath, $destinationPath);
		}
		
	}
	
	function prepareLoadedImage($id,$opt = array()){
		// utk load form 
		
        if(empty($id) || !is_numeric($id)) return;
        
		$rsItemFile = (!isset($opt['file'])) ? $this->getItemImages($id) : $opt['file'];
		$uploadFileFolder =  (!isset($opt['uploadFileFolder'])) ? $this->uploadFolder : $opt['uploadFileFolder'];
		
		if (count($rsItemFile) > 0) {
			$sourcePath = $this->defaultDocUploadPath . $uploadFileFolder . $id;
			$destinationPath = $this->uploadTempDoc . $uploadFileFolder . $id;

			$this->deleteAll($destinationPath);

			if (!is_dir($destinationPath)) mkdir($destinationPath,  0755, true);

			$this->fullCopy($sourcePath, $destinationPath);
		}
		
	}
	
	 function fullCopy( $source, $target ) {
		
		if(substr($source,-1) == "/") {
			$source = substr($source,0,-1);
		}
	
	
		if ( is_dir( $source ) ) {
			@mkdir( $target );
			$d = dir( $source );
			while ( FALSE !== ( $entry = $d->read() ) ) {
				if ( $entry == '.' || $entry == '..' ) {
					continue;
				}
				$Entry = $source . '/' . $entry; 
				if ( is_dir( $Entry ) ) {
					$this->fullCopy( $Entry, $target . '/' . $entry );
					continue;
				}
				copy( $Entry, $target . '/' . $entry );
			}
	 
			$d->close();
		}else {
			copy( $source, $target );
		}
	}
	
	 function getRandomData($total = 5, $status = '(1)' , $addCriteria = '', $orderByCriteria = ''  ){
	 		
				$result = array ();
	  			
				
				$this->setCriteria( ' and '.$this->tableName.'.statuskey in  '.$status.' ' .$addCriteria  );
			  	$query = $this->getQuery() . $orderByCriteria ;  
			   
				
				$rs = $this->oDbCon->doQuery($query);
				 
				if ( count($rs) == 0 )
					return $result;
				  
				if (count($rs) < $total) { 
					$total = count($rs) ;  
				} 
			
				if ($total == 1) {
					$rand_keys[0] =  array_rand($rs, $total); 
				}
				else{ 
					$rand_keys = array_rand($rs, $total); 
				}  		 
			 
				
				
				for ($i=0;$i<$total;$i++) {
					$query = 'select '.$this->tableName  .'.* from '.$this->tableName  .' where '.$this->tableName  .'.pkey = ' . $rs[$rand_keys[$i]]['pkey'] ; 	
	 			 	$rsTemp = $this->oDbCon->doQuery($query);
					array_push($result,$rsTemp[0]);	 
				} 
	 	
			return $result;
	  }
	  
    //function sendMail($from,$subject,$msg,$to, $etc = array()){
	 //$smtpAgent = strtolower($this->loadSetting('SMTPAgent'));
    //    
    //    if (empty($from['email']))   $from['email'] = 'no-reply@'.$this->domain;
    //    if (empty($from['name']))   $from['name'] = $this->loadSetting('companyName');
    //    
    //    switch($smtpAgent){
    //        
    //        case 'sendinblue':
    //            $this->includeClassDependencies(array('SendInBlue.class.php'));
    //            $sendInBlue = new SendInBlue();
    //             
    //            $recipients = array();
    //            array_push($recipients,array('name' => $to['name'], 'email' => $to['email']));
    //            $sendInBlue->sendMail(
    //                  array('name' => $from['name'], 'email' => $from['email']), $subject, $recipients,$msg
    //            );
 //
    //            break;
    //          
			//case 'native' :
				//                
    //            // Always set content-type when sending HTML email
    //            $headers = "MIME-Version: 1.0" . "\r\n";
    //            $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
//
    //            // More headers 
    //            $headers .= 'From: '.$from['email'].' <'.$from['email'].'>' . "\r\n"; 
//
    //            if (!empty($from['email']))
    //                $headers .= 'Reply-To: '.$from['email'].' <'.$from['email'].'>' . "\r\n"; 
//
    //            mail($to['email'],$subject,$msg,$headers);
				//
				//break;
    //             
    //            
    //        default: 
 //
    //            try{ 
    //   
    //                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    //                
				//	// //Server settings
				//	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
				//
    //                if($smtpAgent == 'gmail'){
    //                     $mail->isSMTP();                                            //Send using SMTP
    //                     $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    //                     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    //                     $mail->Username   = $this->loadSetting('SMTPEmailUsername');                     //SMTP username
    //                     $mail->Password   = $this->loadSetting('SMTPEmailPassword');  //                                 //SMTP password
    //                    
    //                     //$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    //                    $mail->Port = 465;
    //                     //$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` 
//
    //                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    //                    $mail->SMTPDebug = 2;
    //                    //$mail->SMTPOptions = [
    //                    //        'ssl' => [
    //                    //            'verify_peer' => false,
    //                    //            'verify_peer_name' => false,
    //                    //            'allow_self_signed' => true
    //                    //        ]
    //                    //];
    //                    
    //                     $this->setLog($mail->Host,true);
    //                     $this->setLog($mail->Username,true);
    //                     $this->setLog($mail->Password,true);
    //                     $this->setLog($mail->SMTPSecure,true);
    //                     $this->setLog($mail->Port,true);
    //                }
    //                
				//	//Recipients
				//	$mail->setFrom($from['email'], $from['name']);
				//	$mail->addAddress($to['email'],$to['name']);     //Add a recipient
				//	// $mail->addAddress('ellen@example.com');               //Name is optional
				//	// $mail->addReplyTo('info@example.com', 'Information');
				//	// $mail->addCC('cc@example.com');
				//	
				//	if(isset($etc['cc']) && !empty($etc['cc'])){
				//		foreach($etc['cc'] as $row) 
				//			$mail->addCC($row);
				//	}
				//	
				//	if(isset($etc['bcc']) && !empty($etc['bcc'])){
				//		foreach($etc['bcc'] as $row) 
				//				$mail->addBCC($row);
				//	}
				//	
				//	// //Attachments
				//	if(isset($etc['attachment']) && !empty($etc['attachment'])){
				//		foreach($etc['attachment'] as $row) 
				//		 $mail->addAttachment($row);    
				//	}
				//	// $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
				//	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
//
				//	//Content
				//	$mail->isHTML(true);                                  //Set email format to HTML
				//	$mail->Subject = $subject;
				//	$mail->Body    = $msg;
				//	$mail->AltBody = strip_tags($msg);
//
				//	$mail->send();
//
				//} catch (Exception $e) {
				//	$this->setLog("Message could not be sent. Mailer Error: {$mail->ErrorInfo}",true);
				//}
    //            
    //            
    //            break;
    //    }
//
	//}
    
        function sendMail($from,$subject,$msg,$to, $etc = array()){
				 	
        $smtpAgent = strtolower($this->loadSetting('SMTPAgent'));
        
        if (empty($from['email']))   $from['email'] = 'no-reply@'.$this->domain;
        if (empty($from['name']))   $from['name'] = $this->loadSetting('companyName');
        
        switch($smtpAgent){
            
            case 'sendinblue':
                $this->includeClassDependencies(array('SendInBlue.class.php'));
                $sendInBlue = new SendInBlue();
                 
                $recipients = array();
                array_push($recipients,array('name' => $to['name'], 'email' => $to['email']));
                $sendInBlue->sendMail(
                      array('name' => $from['name'], 'email' => $from['email']), $subject, $recipients,$msg
                );
 
                break;
              
			case 'native' :
				                
                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

                // More headers 
                $headers .= 'From: '.$from['email'].' <'.$from['email'].'>' . "\r\n"; 

                if (!empty($from['email']))
                    $headers .= 'Reply-To: '.$from['email'].' <'.$from['email'].'>' . "\r\n"; 

                mail($to['email'],$subject,$msg,$headers);
				
				break;
                 
                
            default: 

				 
                     try{ 
       
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    
					// //Server settings
					// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
				
                    if($smtpAgent == 'gmail'){
                         $mail->isSMTP();                                            //Send using SMTP
                         $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                         $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                         $mail->Username   = $this->loadSetting('SMTPEmailUsername');                     //SMTP username
                         $mail->Password   = $this->loadSetting('SMTPEmailPassword');  //                                 //SMTP password
                        
                         //$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                         //$mail->Port = 465;
                         $mail->Port = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` 

                        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                        //$mail->SMTPDebug = 2;
                        //$mail->SMTPOptions = [
                        //        'ssl' => [
                        //            'verify_peer' => false,
                        //            'verify_peer_name' => false,
                        //            'allow_self_signed' => true
                        //        ]
                        //];
                        
                         //$this->setLog($mail->Host,true);
                         //$this->setLog($mail->Username,true);
                         //$this->setLog($mail->Password,true);
                         //$this->setLog($mail->SMTPSecure,true);
                         //$this->setLog($mail->Port,true);
                    }
                    
					//Recipients
					$mail->setFrom($from['email'], $from['name']);
					$mail->addAddress($to['email'],$to['name']);     //Add a recipient
					// $mail->addAddress('ellen@example.com');               //Name is optional
					// $mail->addReplyTo('info@example.com', 'Information');
					// $mail->addCC('cc@example.com');
					
					if(isset($etc['cc']) && !empty($etc['cc'])){
						foreach($etc['cc'] as $row) 
							$mail->addCC($row);
					}
					
					if(isset($etc['bcc']) && !empty($etc['bcc'])){
						foreach($etc['bcc'] as $row) 
								$mail->addBCC($row);
					}
					
					// //Attachments
					if(isset($etc['attachment']) && !empty($etc['attachment'])){
						foreach($etc['attachment'] as $row) 
						 $mail->addAttachment($row);    
					}
					// $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
					// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

					//Content
					$mail->isHTML(true);                                  //Set email format to HTML
					$mail->Subject = $subject;
					$mail->Body    = $msg;
					$mail->AltBody = strip_tags($msg);

					$mail->send();

				} catch (Exception $e) {
					$this->setLog("Message could not be sent. Mailer Error: {$mail->ErrorInfo}",true);
				}
                break;
        }

	}
	
	function memberLogin ($userName='',$password=''){
         
        $sql = 'select pkey from customer where username = ' . $this->oDbCon->paramString($userName);    
        $rs = $this->oDbCon->doQuery($sql);
        
        $id = 0;
        
        if (!empty($rs))
            $id = $rs[0]['pkey']; 
	 	
        $pass = hash('sha256',md5($password));
        
        $returnVal ='
				select 
					*
				from
					customer
				where
					username = '.$this->oDbCon->paramString($userName).' and		
					password = '.$this->oDbCon->paramString($pass).'    
			';		 
		$rs = $this->oDbCon->doQuery($returnVal);
		 
	 	return $rs;
	}
	
	function dateInterval($dateFrom,$dateTo, $format = '%a'){
        //$dateFrom = new DateTime('2010-01-01'); 
        $dateFrom = new DateTime($dateFrom); 
        $dateTo = new DateTime($dateTo); 
        $interval = $dateTo->diff($dateFrom);

        // %a will output the total number of days.
        return $interval->format($format);

        // While %d will only output the number of days not already covered by the
        // month.
        //echo $interval->format('%m month, %d days');    
    }
    
	function dateDiff($dateFrom,$dateTo,$separator=' / ',$format='U'){
		// $dt format indonesia => DD MM YYYY
		// return : second
		 
		// datefrom 
		$strs = explode($separator, $dateFrom);
		$day = $strs[0];
		$month = $strs[1];
		$strs = explode(" ", $strs[2]);
		$year = $strs[0];
		
		$hour = 0;
		$min = 0;
		$sec = 0;
		
		if (!empty($strs[1])) {
			$strs = explode(":", $strs[1]);
			$hour = $strs[0];
			$min =  $strs[1];
			if(!empty($strs[2]))
				$sec =  $strs[2];
		}
		 
		$dateFrom = date($format, mktime((int)$hour,(int)$min,(int)$sec, (int)$month, (int)$day, (int)$year )) ;
		
		
		
		// dateto 
		$strs = explode($separator, $dateTo);
		$day = $strs[0];
		$month = $strs[1];
		$strs = explode(" ", $strs[2]);
		$year = $strs[0];
		
		$hour = 0;
		$min = 0;
		$sec = 0;
		
		if (!empty($strs[1])) {
			$strs = explode(":", $strs[1]);
			$hour = $strs[0];
			$min =  $strs[1];
			if(!empty($strs[2]))
				$sec =  $strs[2];
		}
		 
		$dateTo = date($format, mktime((int)$hour,(int)$min,(int)$sec,(int) $month,(int) $day,(int) $year )) ;
		
		
		return $dateTo - $dateFrom;
		
	}
	
	function formatDbDate($dt,$format='',$returnOnEmpty = array()){
		// $dt format mysql => YYYY MM DD
        $arrEmptySet = array('0000-00-00', '0000-00-00 00:00', '0000-00-00 00:00:00', '1970-01-01', 
							 '1970-01-01 00:00:00', '1970-01-01 07:00', '1970-01-01 07:00:00', '01 / 01 / 1970', '01 / 01 / 1970 00:00:00',
							 '30 / 11 / 1999');
        if(isset($returnOnEmpty['returnOnEmpty']) && $returnOnEmpty['returnOnEmpty'] == true){
          if( empty($dt) || in_array( $dt , $arrEmptySet) ) 
                return (!isset($returnOnEmpty['value'])) ?  '00 / 00 / 0000' : $returnOnEmpty['value'];
        }
     
        
		if (empty($format))
			$format = 'd / m / Y' ;
			
		$strs = explode("-", $dt);
		$year = $strs[0];
		$month = $strs[1];
		$strs = explode(" ", $strs[2]);
		$day = $strs[0];
		
        
		$hour = 0;
		$min = 0;
		$sec = 0;
		
		if (!empty($strs[1])) {
			$strs = explode(":", $strs[1]);
			$hour = $strs[0];
			$min =  $strs[1];
			if(count($strs) == 3)
				$sec =  $strs[2];
		}
		 
		return date($format, mktime((int)$hour,(int)$min,(int)$sec, (int)$month, (int)$day, (int)$year ));
		
	}
	 
	 
  	function checkPassword($id,$username,$password){
			$sql = 'select 
                        * 
                    from
                        '.$this->tableName .' 
                    where 
                        pkey = '  .$this->oDbCon->paramString($id) . '  and 
                        username = '.$this->oDbCon->paramString($username).' and 
                        password =  '.$this->oDbCon->paramString(hash('sha256',md5($password)));	
         
			$rs = $this->oDbCon->doQuery($sql);
 			
			if (count($rs) ==0)
				return false;
			else
				return true; 
	}
	  
	
	function setLog($msg,$alwaysShow = false ,$filename = ''){ 
	   
        if(!$alwaysShow && !DEBUG)  return; 
        
        if(is_array($msg)) $msg = print_r($msg, true);
        
	 	$path = DOC_ROOT.'log/';
		$path .= (DOMAIN_NAME) ? DOMAIN_NAME.'/' : '';
            
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		} 
		
        $filename = (empty($filename)) ? '['.date('d-m-Y') .'] - '.md5(DOC_ROOT).'.txt' : $filename; 
        $filename = $path.$filename; 
				   
		error_log ($msg.chr(13),3,$filename);
	}
	
    
    function setTimeLog($msg,$alwaysShow = false ,$title = ''){  
       $timeStamp = date('d-m-Y H:i:s');    
	   $this->setLog($timeStamp .' ==> '.$msg,$alwaysShow,$title);
	}
    	
	function updateOrder ($orderlist,$id){
			if ($this->oDbCon->startTrans()){
				
				$sql = 'select pkey from ' . $this->tableName . ' where pkey <> '.$this->oDbCon->paramString($id).' and orderlist >= ' . $this->oDbCon->paramString($orderlist) .' order by orderlist asc' ;
				$rs =  $this->oDbCon->doQuery($sql);
					
				for ($i=0;$i<count($rs);$i++){
					$orderlist++;
					$sql = 'update ' . $this->tableName . ' set orderlist = '.$orderlist.' where pkey = ' . $rs[$i]['pkey'] ;			
					$this->oDbCon->execute($sql);	
				}
		 
				//$this->addTrail($sql);
				$this->oDbCon->endTrans(); 
				return true;				
				
			}else {
				$this->oDbCon->rollback();
				return false;
			}
					
	}
	
    function getItemImages($pkey, $tableImage = ''){
        
        if(!is_array($pkey)) $pkey = array($pkey);
        
        if(empty($tableImage))
            $tableImage = $this->tableImage;
            
		$sql = 'select * from '.$tableImage.' where refkey in ('.$this->oDbCon->paramString($pkey,',').') order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
     } 
  
    
    function getFileDetail($pkey, $tableFile = '',$criteria='',$orderby =''){
		
        if(!is_array($pkey)) $pkey = array($pkey);
        
		if(empty($tableFile))
			$tableFile = $this->tableFile;
			
		$sql = 'select * from '.$tableFile.' where refkey in ('.$this->oDbCon->paramString($pkey,',').') order by orderlist asc, pkey asc';	
        
        if(!empty($criteria))  $sql .= ' ' .$criteria; 
        if(!empty($orderby))  $sql .= ' ' .$orderby;
          
		return $this->oDbCon->doQuery($sql);
    }
	
   function updateImages($pkey,$token,$arrImage, $tableImage = '', $path = '' ){		
         
        if(empty($tableImage) && isset($this->tableImage))
            $tableImage = $this->tableImage;
       
        if(empty($path))
            $path = $this->uploadFolder;
          
		$sourcePath = $this->uploadTempDoc.$path.$token;
		$destinationPath = $this->defaultDocUploadPath.$path;
			
		if(!is_dir($sourcePath)) 
			return;
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		  
		//delete previous images	    
		$this->deleteAll($destinationPath);  
       
        if(!empty($tableImage)){ 
            $sql = 'delete from '.$tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
            $this->oDbCon->execute($sql);
        }
		  
		if (!empty($arrImage))	{
			
			$arrImage = explode(",",$arrImage);
				
            if(!empty($tableImage)){  
                for ($i=0;$i<count($arrImage);$i++){   

                    if ($i >= PLAN_TYPE['maxproductimage'])
                        continue;

                    $newFileName = $this->hashFileName($sourcePath.$arrImage[$i]);
                    $imagekey = $this->getNextKey($tableImage);  
                    $this->uploadImage($sourcePath, $destinationPath,$arrImage[$i],$newFileName);

                    // get file size
                    $filefullpath = $destinationPath.'/'.$newFileName;
                    $arrSizeInformation = getimagesize($filefullpath);
                    $width = $arrSizeInformation[0];
                    $height = $arrSizeInformation[1]; 
                    $size = filesize($filefullpath);
 
                    $sql = 'insert into '.$tableImage.' (pkey,refkey,file,width,height,size) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($newFileName).','.$this->oDbCon->paramString($width).','.$this->oDbCon->paramString($height).','.$this->oDbCon->paramString($size).')';	
                    $this->oDbCon->execute($sql);	  
                }	
            }else{
                $this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			    return $arrImage[0]; 
            } 
				
		}  
					
        return;
	} 
    
    function uploadFile($sourcePath, $destinationPath, $fileName,$newFileName='',$deleteDestination=false){ 
		// khusus upload file satuan
		
        $this->validateDiskUsage();
      
		// kalo gk, folder yg udah kebuat otomatis kehapus lg kalo gk taro di else
		if($deleteDestination && is_dir($destinationPath)) 
			$this->deleteAll($destinationPath);
		
		// kalo gk ad file, return saja (karena user hapus dr UI, bedakan dengan filename ad tp fiel fisik gk ada
		if($fileName == '') return;
		 
		if(!is_dir($destinationPath)){ mkdir ($destinationPath,  0755, true);} 
	  
	    if (empty($newFileName))
	        $newFileName = $fileName;
	         
		//always change to 0755
		chmod($destinationPath,  0755);
	 	 
		 if(substr($sourcePath,-1) <> "/")   $sourcePath  .= '/';  
		 if(substr($destinationPath,-1) <> "/")   $destinationPath  .= '/';  
 
        // cek exist gk filenya
        $sourceFileName = $sourcePath.$fileName;  
		
        if(!is_file($sourceFileName))  
            throw new Exception($this->errorMsg[217]. '<br>' .$fileName);
		
		rename($sourceFileName,$destinationPath.$newFileName);
		
	} 
    
  function uploadImage($sourcePath, $destinationPath,$fileName,$newFileName=''){ 
		 
        $this->validateDiskUsage();
      
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
	  
	    if (empty($newFileName))
	        $newFileName = $fileName;
	        
		//always change to 0755
		chmod($destinationPath,  0755);
	 	 
		 if(substr($sourcePath,-1) <> "/")   $sourcePath  .= '/';  
		 if(substr($destinationPath,-1) <> "/")   $destinationPath  .= '/';  
	
        // cek exist gk filenya
        $sourceFileName = $sourcePath.$fileName;
        if(!is_file($sourceFileName))
            throw new Exception($this->errorMsg[217]. '<br>' .$fileName); 
       
	    rename($sourceFileName,$destinationPath.$newFileName);
	} 
	
  function validateDiskUsage(){
       
        if (PLAN_TYPE['maxdiskusage'] < 0 ) return;
            
        $unit = 'GB'; 
        $usage =  $this->convertSize(PLAN_TYPE['maxdiskusage'],$unit,'MB');
      
        // cek limit
        $security = new Security();
        $rsPlan = $security->getUserPlanType();
       
        if ( $rsPlan[0]['useddiskusage']  > $rsPlan[0]['maxdiskusage'] )
            throw new Exception($this->errorMsg['limit'][6] .' ('.$this->lang['max'].' '. $this->formatNumber($usage,0). ' '.$unit.')' );
         
  }
  
    function loadTableKeySettings(){
        $sql = 'select pkey,tablename,timestamptype,minstatustoprint,decimalnumber,defaultstatus,locktransactiondate,usecashbankvoucher,datepickermaxdays,datepickermindays,datepickername, isautoclose, printjournal from '.$this->tablekey ;
        return $this->oDbCon->doQuery($sql); 
    }
	  
//    function loadSystemSettings(){
//        $sql = 'select '.$this->tableSettings.'.code,'.$this->tableUserSettings.'.value 
//				from '.$this->tableUserSettings.','.$this->tableSettings.'
//				where '.$this->tableSettings.'.pkey = '.$this->tableUserSettings.'.settingkey';
//		
//        return $this->oDbCon->doQuery($sql); 
//    }
	
	function formatNumber($number,$decimal=-1,$thousandseparator=',',$decimalseparator = DECIMAL_SEPARATOR){
        
        // number : -2// jd -3 aj
        // accounting : null
        // integer : 0
        // decimal : 2
         
        // perlu cek kalo dikirim 0, kayanya dianggap empty, jadinya -1 
        // tp sementara anggap aj kalo integer WAJIB mengikuti settingan tablekey
        
        if (!is_numeric($number))  return 0 ;
          
        // kalo autodecimal, jgn ambil dr tablekey
        // nanti autodecimal harus dipisah seeprtinya... agar ad jenis yg memang selalu auto ?
        
        // khusus format number  dan kalo gk dikirim
        if($decimal == -1 || $decimal == -3 ){
            $decimalNumber = $this->getTableKeyAndObj($this->tableName,array('decimalnumber'))['decimalnumber'];
            $decimalNumber = (!empty($decimalNumber)) ?  $decimalNumber : 0; 
        }
        
        
        if ($decimal == -1){    
          
//            $decimalNumber = $this->getTableKeyAndObj($this->tableName,array('decimalnumber'))['decimalnumber'];
//            $decimalNumber = (!empty($decimalNumber)) ?  $decimalNumber : 0; 
            
            if (!empty($decimalNumber))
              $decimal = $decimalNumber;
            
        }else if($decimal == -2 || $decimal == -3){ 
            // kalo decimal, return koma,
            // kalo gk ad decimal, return bilangan bulat
            // kalo tablekey gk sama dengan nol, maka ikut settingan tablekey
            
            
            if (!empty($decimalNumber))
              $decimal = $decimalNumber;
            else
              $decimal = ($this->is_decimal($number)) ? 2 : 0;   // kalo pake abs(decimal) coba cek di item list, knp gramasina jd 3 decimal
        } 
       
		return number_format($number,$decimal,$decimalseparator,$thousandseparator) ;
	}
	
	function unFormatNumber($val){
        if ($val == '') return 0;
        
		return str_replace(',','',$val); 
	}
    
    function is_decimal( $val ){
        return is_numeric( $val ) && floor( $val ) != $val;
    }
	   
	function setTransactionLog($actiontype,$pkey,$desc = '', $reason = ''){
        $userkey = 0;
        
        if (!empty($_SESSION[$this->loginAdminSession]['id']))
		  $userkey = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        
/*	  	$rs = $this->getDataRowById($pkey);	 
        if(empty($rs))
            return;*/
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        
		$sql =  'insert into 
					'. $this->transactionLog.' ( 
						createdon,
						createdby,
						actionkey,  
						tablekey,
						refkey ,
						trdesc,
						reason 
					) values( 
						now(),
						'.$this->oDbCon->paramString($userkey).',
						'.$this->oDbCon->paramString($actiontype).',  
						'.$this->oDbCon->paramString($rsKey['key']).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($desc).',
						'.$this->oDbCon->paramString($reason).'  
					)'; 
		return $this->oDbCon->execute($sql);	
	}
	 
    function getTransactionAuditLog($tableName, $arrPkey){
        // $tableName dikirim karena dari array
        
         $arrPkey = array_unique($arrPkey);
        
         $sql = 'select '.$tableName.'.pkey,
                                '.$this->tableEmployee.'.name as createdbyname,
                                '.$tableName.'.createdby ,
                                '.$tableName.'.createdon 
                        from ' . $tableName .',  '.$this->tableEmployee.'
                        where '.$this->tableEmployee.'.pkey = '.$tableName.'.createdby and
                                '.$tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey, ',').')';  
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
    }
	   
	function addErrorList(&$arrayToJs,$condition,$label, $responseCode = array()){
        array_push($arrayToJs,array("valid" => $condition, "message" => $label, "response" => $responseCode));   
	}
	
	function addErrorLog($condition,$label, $throwIfHasErrorLog = false){    
            $this->oDbCon->addErrorLog($condition,$label,$throwIfHasErrorLog);
	}
    
    function startNewErrorLogSession(){
        return $this->oDbCon->startNewErrorLogSession();
    }
    
    function resetErrorLog(){
        return $this->oDbCon->resetErrorLog();
    }
    
    function getErrorLog(){
            return $this->oDbCon->getErrorLog();
    }
    
    function  throwIfHasErrorLog(){
       $this->oDbCon->throwIfHasErrorLog(); 
    }
	
	function useAutoCode($table_name){	
        
        // harus update, kalo ada custom code, bisa tetep manual
        $customCode = new CustomCode();
        $sql = 'select 
                    '.$this->tableCustomCode.'.useautocode
                from 
                    '.$this->tablekey.','.$this->tableCustomCode.' 
                where 
                    '.$this->tablekey.'.tablename = '.$this->oDbCon->paramString($table_name).' and
                    '.$this->tableCustomCode.'.reftabletype = '.$this->tablekey.'.pkey ';
        
        $rsCustomCode = $this->oDbCon->doQuery($sql); 
         
        if(!empty($rsCustomCode)){
            return ($rsCustomCode[0]['useautocode'] == 1) ? true : false; 
        }else{ 
            $sql = 'select * from _code where code = '.$this->oDbCon->paramString($table_name);  
            $rsUseAutoCode = $this->oDbCon->doQuery($sql);   
            return ($rsUseAutoCode[0]['useautocode'] == 1) ? true : false;    
        }
        
        return false;
        
	}  
    
    function getCountedTotalRows($rsCount, $filterkey='',$itemkey=''){
        
        $total = 0 ;
        
        foreach($rsCount as $row){ 
            if (!empty($filterkey) && $row[$filterkey] != $itemkey)  continue; 
            $total += $row['totalrows'];
        }
         
        return $total;
    }
    
	function countTotalRows($criteria, $groupby = array()){ 
        
		$this->setCriteria($criteria); 
        
        $query = $this->getQueryForList();
        if (empty($query))
            $query = $this->getQuery();
        
        $sql = strtolower($query); 
        
       // $sql = strtolower($this->getQuery()); 
           
        $temp =  preg_split("/\bfrom\b/iu", $sql,2); //explode(' from ',$sql,2); 
        
        $groupfield  = '';
        foreach($groupby as $key => $row) 
           $groupfield .=   $row['fieldName'] .' as ' .  $row['groupkey'] .','; 
       
        $selectField = array();
        foreach($this->filterCriteria as $row){
            if(strpos($row['field'], '.') !== false)
                array_push($selectField, $row['field']. ' as '.$row['alias']);
            else    
                array_push($selectField, $this->tableName.'.'. $row['field']);
        }
        
        $sql = 'select '. $this->tableName.'.pkey, '. $this->tableName.'.tagkey, '.implode(',',$selectField).' from ' . $temp[1];  
        $sql = 'select '.$groupfield.' coalesce(count('.$this->tableName.'.pkey),0) as totalrows from (' .$sql.') ' . $this->tableName ;
              
        if (!empty($groupby)){  
            $groupByField = array_column($groupby,'fieldName');
            $sql .= ' group by ' . implode(', ',$groupByField);      
        }
        
		$rs  = $this->oDbCon->doQuery($sql);
		return $rs;
		
	} 
    
	function getTotalRows($criteria, $groupby = ''){
		// fungsi ini sudah hampir gk kepake
        
		$this->setCriteria($criteria); 
        
        $sql = $this->getQuery();
        
        $sql .= $groupby;
        
		$rs  = $this->oDbCon->doQuery($sql);
		return count($rs);
        
        /*$this->setCriteria($criteria); 
        $sql = strtolower($this->getQuery()); 
        $temp =  preg_split("/\bfrom\b/iu", $sql,2); //explode(' from ',$sql,2); 
        
        $sql = 'select '.$this->tableName.'.pkey from '.$temp[1];
        
        $sql .= $groupby;
         
        $this->setLog($sql,true);
        $this->setLog("==================",true);
        
        $rs  = $this->oDbCon->doQuery($sql);
		return count($rs);*/
	} 
	 
	 function changeTag($id, $newTag){
		 
		 try{
			
			$arrayToJs =  array();
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
					 
			$sql = 'update ' . $this->tableName . ' set tagkey = ' . $this->oDbCon->paramString($newTag) . ' where pkey = ' . $this->oDbCon->paramString($id) ;
			$this->oDbCon->execute($sql);
            
            if($newTag == 0){ 
                $this->setTransactionLog(UPDATE_DATA,$id,'Hapus Tag'); 
            }else{
                $rsTag = $this->getAllTag();
                $rsTag = array_column($rsTag, null, 'tagkey'); 
                $this->setTransactionLog(UPDATE_DATA,$id,'Update Tag '. $rsTag[$newTag]['tagname']); 
            }
            
			$this->oDbCon->endTrans();
			
			$arrayToJs[0]['valid'] = true;
			$arrayToJs[0]['message'] = $this->lang['dataHasBeenSuccessfullyUpdated'];
			
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			 
			$arrayToJs[0]['valid'] = false;
			$arrayToJs[0]['message'] = $e->getMessage(); 
		}		 
 		return $arrayToJs; 
		 
	 }
	  
	 function generateTimeStamp($obj,$id){
		$employee = new Employee();
		 
		$rstimeStamp = $obj->getDataRowById($id);
		$rsCreator = $employee->getDataRowById($rstimeStamp[0]['createdby']);
		$rsModifier = $employee->getDataRowById($rstimeStamp[0]['modifiedby']);
	
		
		$timestamp = '<div style="clear:both;"></div>';	
		$timestamp .= '<div class="timestamp">';
		$timestamp .= $rsCreator[0]['name'].' &bull; '.$obj->formatDbDate($rstimeStamp[0]['createdon'],'d / m / Y H:i:s') .'<br>'; 
		$timestamp .= $rsModifier[0]['name'].' &bull; '.$obj->formatDbDate($rstimeStamp[0]['modifiedon'],'d / m / Y H:i:s');
		$timestamp .= '</div>';  
		
		return $timestamp;
	}
	
	
	function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	 
	function getDomain($string){
				
		$domain = parse_url($string);
		
		if (isset($domain["host"])){
			$domain = $domain["host"];
		}else{ 
			$domain = $domain["path"];
		}
		
		 $domain = str_replace('www.','',$domain); 
		 $domain = str_replace('www','',$domain); 
		 
	 	return $domain;
 
	}
	
	function randomPassword($length = 8) {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < $length; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
     
     function validateAutoReverseGL($transactionPkey, $tableName = ''){
         
        $generalJournal = new GeneralJournal();
         
        if (empty($tableName)) $tableName = $this->tableName;
         
        $rsKey = $this->getTableKeyAndObj($tableName,array('key')); 
        $rs = $generalJournal->searchData('refkey',$transactionPkey,true,' and reftabletype = '.$this->oDbCon->paramString($rsKey['key']));   
        $security = new Security();
        $hasAccess = $security->isAdminLogin('autoReverseGL',10);
        
        if($hasAccess) return true;
          
        for($i=0;$i<count($rs);$i++)
            if ($rs[$i]['statuskey'] == TRANSACTION_STATUS['selesai'] && !$hasAccess) return false;     
          
        return true;
     }
    
     function cancelGLByRefkey ($transactionPkey,$tableName){ 
        //if ($this->loadSetting('useGL') <> 1)
        //    return;
         
        // PERLU CEK KALO SUDAH CLOSING GK BISA DICANCEL
        
        //  cancel GL
		if(!USE_GL) return;
		 
        $generalJournal = new GeneralJournal();
         
        $rsKey = $generalJournal->getTableKeyAndObj($tableName,array('key')); 
        $rs = $generalJournal->searchData('refkey',$transactionPkey,true,' and reftabletype = '.$this->oDbCon->paramString($rsKey['key']));   
          
         for($i=0;$i<count($rs);$i++){
            if ($rs[$i]['statuskey'] != TRANSACTION_STATUS['selesai']){  
                $generalJournal->delete($rs[$i]['pkey'],true); 
            } else {
                    // kalo gk punya akses return buat jaga2 kalo ad yg gk kevalidasi
                    $security = new Security();
                    if(!$security->isAdminLogin('autoReverseGL',10))
                        throw new Exception($this->errorMsg['generalJournal'][6]); 
                
                    //create reverse journal kalo jurnal sudah selesai / closed
                
                    //$sql = 'select * from chart_of_account_active_period where isclosed = 1 order by pkey desc limit 1';
                    //$rsPeriod = $this->oDbCon->doQuery($sql);
                  
                    $sql = 'select 
                                pkey
                            from 
                                chart_of_account_active_period 
                            where 
                                month(runningmonth) = month(\''.$rs[$i]['trdate'].'\') and 
                                year(runningmonth) = year(\''.$rs[$i]['trdate'].'\') and 
                                isclosed = 1 order by pkey desc limit 1';
                
                    $rsPeriod = $this->oDbCon->doQuery($sql);
                
                    $periodkey = $rsPeriod[0]['pkey'];
                
                    $arr = array();
                    $arr['code'] = 'xxxxx';
                    $arr['refkey'] = $rs[$i]['refkey'];
                    $arr['refTableType'] = $rs[$i]['reftabletype']; 
                    $arr['trDesc'] = 'Reverse '. $rs[$i]['code'];
                    $arr['refCode'] = $rs[$i]['refcode'];
                    $arr['trDate'] = date('d / m / Y');
                    $arr['totaldebit'] = $rs[$i]['totalcredit'];
                    $arr['totalcredit'] = $rs[$i]['totaldebit'];
                    $arr['createdby'] = $rs[$i]['createdby'];
                    $arr['cancelForPeriod'] = $periodkey;
                    $arr['reverseFor'] = $rs[$i]['pkey'];
                
                    $rsDetail = $generalJournal->getDetailById($rs[$i]['pkey']);
                    $arr['hidCOAKey'] = array();
                    $arr['debit'] = array();
                    $arr['credit'] = array();
                    $arr['trdescDetail'] = array();
                
                    for($j=0;$j<count($rsDetail);$j++){
                        array_push($arr['hidCOAKey'],$rsDetail[$j]['coakey']);
                        array_push($arr['debit'],$rsDetail[$j]['credit']);
                        array_push($arr['credit'],$rsDetail[$j]['debit']); 
                        array_push($arr['trdescDetail'],$rsDetail[$j]['trdesc']); 
                    }
                 
                    $generalJournal->addData($arr); 
            }
         }
       
    }
     
    function checkTotalItemLimitation($tableName,$limit,$pkey ='', $status = ''){
        
        if ($limit <= 0)
            return false;
        
        $sql = 'select count(pkey) as total from '.$tableName .' where 1=1 ';
        $sql .= $status;
        
        if (!empty($pkey))
            $sql .= ' and pkey <> ' .  $this->oDbCon->paramString($pkey); 
        
        $rs = $this->oDbCon->doQuery($sql); 
        if ($rs[0]['total'] >= $limit)
            return true;
        else 
            return false;
    }
	 
    function generateDataHistoryForTimeline($criteria=''){
        
        //$criteria .= ' and '.$this->tableName.'.code in (\'11-21070003\',\'20-2101000001\') ' ;
        //ambil dulu tablekey nya utk mengurangi waktu load query pas join
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        
        $sql = 'select
                    '.$this->tableName.'.code, 
                    '.$this->tableName.'.trdate, 
                    '.$this->transactionLog.'.createdon, 
                    '.$this->transactionLog.'.actionkey , 
                    '.$this->transactionLog.'.refkey 
                from 
                    '.$this->transactionLog.'  , 
                    '.$this->tableName.'
                where  
                    '.$this->transactionLog.'.tablekey = '.$this->oDbCon->paramString($tablekey).' and
                    '.$this->transactionLog.'.refkey = '. $this->tableName.'.pkey 
                    '.$criteria.'
                order by  
                    '.$this->tableName.'.trdate asc,
                    '.$this->tableName.'.code asc, 
                    '.$this->transactionLog.'.pkey asc '
            ;
        
         //$this->setLog($sql,true);
         $rs = $this->oDbCon->doQuery($sql); 
         
         return $rs;
    }
    
    function calculateDateDiffPerStatus($criteria, $arrOpt = array()){
        $totalPerStatus = array();
        
        $rsStatus = $this->getAllStatus(); 
        $arrStatus = array_column($rsStatus,'pkey');
        array_pop($arrStatus);

        $arrActionKey = $arrStatus;
        array_pop($arrActionKey);

        // cari semua transaksi yg bkn batal
        // cari transaksi actionkey dlm status tertentu
        // yg selesai tetep harus diikutsertakan, agar ad patokan status akhir
        $rsHistory = $this->generateDataHistoryForTimeline(' and (actionkey in (11,'.$this->oDbCon->paramString($arrStatus,',').') )
                                                             and '.$this->tableName.'.statuskey in ('.$this->oDbCon->paramString($arrStatus,',').') '.
                                                             $criteria);

        $rsHistory = $this->reindexDetailCollections($rsHistory,'refkey');    
        //$this->setLog($rsHistory,true);
        
        // hitung dulu brp interval 
        //$totalSalesRow = count($rs);
        $transactionRow = array();
        $totalPerStatus = array(); 
        foreach($rsHistory as $refkey=>$historyRow){
            $dateDiff = $this->calculateTimeline($historyRow,$arrOpt);  
            
            $transactionRow[$refkey] = array();
            $transactionRow[$refkey]['code'] = $historyRow[0]['code'];
            $transactionRow[$refkey]['trdate'] = $historyRow[0]['trdate'];
                
            foreach($dateDiff as $key => $dateDiffRow){
                if (!isset($totalPerStatus[$key])){
                  $totalPerStatus[$key]['datediff'] = 0;  
                  $totalPerStatus[$key]['ctr'] = 0;     
                } 

                $totalPerStatus[$key]['datediff'] += $dateDiffRow;
                $totalPerStatus[$key]['ctr']++;
                
                // waktu status per refkey
                if (!isset( $transactionRow[$refkey]['statusTimeline'][$key] )) $transactionRow[$refkey]['statusTimeline'][$key] = 0;     
                $transactionRow[$refkey]['statusTimeline'][$key] += $dateDiffRow;  
            }
        }
        
        $intervalLabel  = $this->lang['day(s)'];
        if(isset($arrOpt['interval'])){
            
            switch($arrOpt['interval']){
                case 'h':$intervalLabel  = $this->lang['hour(s)'];
                        break;
                default : $intervalLabel  = $this->lang['day(s)'];

            }
        }
        
        //foreach($totalPerStatus as $statuskey=>$row){ 
        foreach($arrActionKey as $statuskey){ 
            $avgDays = ($totalPerStatus[$statuskey]['ctr'] > 0) ? $totalPerStatus[$statuskey]['datediff']/$totalPerStatus[$statuskey]['ctr'] : 0;
            $totalPerStatus[$statuskey]['label'] = $this->formatNumber($avgDays,2).' ' .strtolower($intervalLabel); 
        }
        
        return array(
                'totalPerStatus' => $totalPerStatus,
                'transactionRows' => $transactionRow,
        ) ;
    }
    
    function generateDataHistory($id,$year='',$criteria=''){
        
        if (empty($id))  return array();
        
        if(!is_array($id)) $id = array($id);
         
        $tableName = $this->transactionLog;
        
        // kalo thn sebelumnya , ambil dr archive
        $currYear = date('Y');
        $conn =  $this->oDbCon;
		
		
		
        if(!empty($year) && $year < $currYear){ 
            
            try{
                $tableName = $this->transactionLog.'_'.$year;

                // cek ad gk tablenya
                $rsTable = array();
                $conn = $this->archiveConn();
                
                $sql = 'show tables like '.$this->oDbCon->paramString($tableName);
                $rsTable = $conn->doQuery($sql);
               
 
                if(empty($rsTable)) return array();
                
                
                // karena archive tdk ad tablekey dan employee, maka harus select manual
                
                $sql = 'select '.$this->tablekey.'.pkey from '.$this->tablekey.' 
                        where 
						'.$this->tablekey.'.tablename = ' . $this->oDbCon->paramString($this->tableName).' and
						'.$this->tablekey.'.statuskey = 1 ';
                $rsTableKey = $this->oDbCon->doQuery($sql);
                
                $tablekey = (!empty($rsTableKey)) ? $rsTableKey[0]['pkey'] : 0;
                
                // log action                
                $sql = 'select '.$this->transactionLogAction.'.* from '.$this->transactionLogAction;
                $rsTransactionLogAction = $this->oDbCon->doQuery($sql);
                $rsTransactionLogAction = array_column($rsTransactionLogAction,'name','pkey');
                
                
                $sql = 'select
                    '.$tableName.'.*
                from 
                    '. $tableName.'   
                where 
                    '.$tableName.'.tablekey = '. $this->oDbCon->paramString($tablekey).' and
                    '.$tableName.'.refkey in(' . $this->oDbCon->paramString($id,',') .') 
                    '.$criteria.'
                order by  createdon asc, pkey asc ';
                 
                $rs = $conn->doQuery($sql); 
                
                if(empty($rs)) return $rs;
                
                // employee
                $sql = 'select pkey,name from '.$this->tableEmployee.' where '.$this->tableEmployee.'.pkey in ('.$this->oDbCon->paramString( array_column( $rs,'createdby') , ',').')' ;    
                $rsEmployee =  $this->oDbCon->doQuery($sql);
                $rsEmployee = array_column($rsEmployee,'name','pkey');
                
                $totalRs = count($rs);
                for($i=0;$i<count($rs);$i++){
                    $rs[$i]['name'] = $rsTransactionLogAction[$rs[$i]['actionkey']];
                    $rs[$i]['employeename'] = $rsEmployee[$rs[$i]['createdby']];
                }
                
            }catch(Exception $e){
                 return array();
            }
            
            
        }else{
             $sql = 'select
                    '.$tableName.'.*, 
                    '.$this->transactionLogAction.'.name,
                    '.$this->tableEmployee.'.name as employeename 
                from 
                    '. $tableName.' 
                        left join  '.$this->tableEmployee.' on '. $tableName.'.createdby =  '.$this->tableEmployee.'.pkey,
                    '.$this->tablekey.',
                    '.$this->transactionLogAction.'    
                where 
                    '.$this->tablekey.'.tablename = ' . $this->oDbCon->paramString($this->tableName) .' and 
                    '.$this->tablekey.'.pkey = '. $tableName.'.tablekey and
                    '.$tableName.'.actionkey = '.$this->transactionLogAction.'.pkey and
                    '.$tableName.'.refkey  in(' . $this->oDbCon->paramString($id,',') .') 
                    '.$criteria.'
                order by  createdon asc, pkey asc '
            ;
            
           $rs = $this->oDbCon->doQuery($sql); 
        }
         
         return $rs;
    }
    
    function compileDataHistoryForAdminForm($rs,$useHeader = true){
        if(empty($rs))
            return '';
         
        if($useHeader){
            $content = '<div class="history-row"><div class="div-table history-table" style="width: 100%">';
            $content .= '<div class="div-table-row">'; 
                $content .= '<div class="div-table-col-3" style="width: 15em;"><b>'.ucwords($this->lang['actionTime']).'</b></div>';
                $content .= '<div class="div-table-col-3" style="width: 15em;"><b>'.ucwords($this->lang['user']).'</b></div>';
                $content .= '<div class="div-table-col-3" style="width: 15em;"><b>'.ucwords($this->lang['activity']).'</b></div>';
                $content .= '<div class="div-table-col-3"><b>'.ucwords($this->lang['note']).'</b></div>';
            $content .= '</div>'; 
        }
        
            for($i=count($rs)-1;$i>=0;$i--){ 
                $actionName = $this->lang[$rs[$i]['name']];
                $statusname = '';
                if ($rs[$i]['actionkey'] <= 10) {
                    $rsStatus = $this->getStatusById ($rs[$i]['actionkey']);
                    $actionName = $actionName. ': ' . $rsStatus[0]['status'];
                }
                    
                 $employeeName = (!empty($rs[$i]['employeename'])) ? $rs[$i]['employeename'] : USER_SYSTEM['name'];
                  
                 $reason = (!empty($rs[$i]['reason'])) ? '<div class="text-muted"><i>'.$this->replaceNewLine($rs[$i]['reason']).'</i></div>' : '';
                
                 $content .= '<div class="div-table-row">'; 
                    $content .= '<div class="div-table-col-3">'.$this->formatDBDate($rs[$i]['createdon'],'d / m / Y H:i:s').'</div>';
                    $content .= '<div class="div-table-col-3">'.$employeeName.'</div>';
                    $content .= '<div class="div-table-col-3">'.$actionName.$reason.'</div>';
                    $content .= '<div class="div-table-col-3">'.$this->replaceNewLine($rs[$i]['trdesc']).'</div>'; 
                $content .= '</div>'; 
            }   
        
        if($useHeader)
          $content .= '</div></div>'; 
         
        
//        $content .= '
//            <script>$( ".btn-show-history" ).unbind().click(function() { $(this).closest(".history-row").find(".hide").removeClass("hide"); $(this).hide();   }); </script>
//        ';
        
         return $content;
    }
    
    function generatePrintContextMenu($menuname, $filename, $splitWindows = false){
		
		$arrFileName = explode('?',$filename);
		$filename = rtrim($arrFileName[0],'/');
		 
		$param = (isset($arrFileName[1])) ? '/'.$arrFileName[1] : '';
		
        //sementara percuma karena chrome hanya membatasi satu windows
        $return =  '
        case "'.$menuname.'": 
				var selectedTabId = selectedTab.newPanel[0].id;
				var selectedPkey = tabParam[selectedTabId].selectedPkey;  
            ';
           
        if($splitWindows){
            $return .= ' for(var i=0;i<selectedPkey.length;i++){window.open(\''.$filename.'/\' + selectedPkey[i] + \''.$param.'\',"_blank" );}'. chr(13);   
        }else{
            $return .= '  window.open(\''.$filename.'/\' + selectedPkey + \''.$param.'\' );'. chr(13);   
        }
        
        $return .= ' break;'.chr(13) ;    
        
        return $return;
    }
    
     function generateActionContextMenu($menuname, $filename='', $script = ''){
        //sementara percuma karena chrome hanya membatasi satu windows
         
        if (empty($script))
            $script = '  $.ajax({
                            type: "POST",
                            url: "'.$filename.'",
                            data: { pkey: JSON.stringify(selectedPkey) },
                            async : true,
                            success: function(data){      

                                    }
                          }); 

                        alert("'.$this->lang['TheProcessHasBeenRun'].'");';
         
        
         $return =  '
            case "'.$menuname.'": 
                    var selectedTabId = selectedTab.newPanel[0].id;
                    var selectedPkey = tabParam[selectedTabId].selectedPkey; 
                    '.$script.'
                 break;'.chr(13) ;    
        
        
        return $return;
    }
    
     /* 
    function getPrintSetting($modulcode){
        $sql = 'select * from _print_setting where modulecode = ' . $this->oDbCon->paramString($modulcode);
        $rs = $this->oDbCon->doQuery($sql); 
        
        if (empty($rs))
            return 0;
        
        return $rs[0];
    }*/
     
    
    function validateForm($arr,$pkey = ''){
       
		$arrayToJs = array();
         
        if(!empty($pkey)){
            $latestModifiedOn = $arr['hidModifiedOn'];
            
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['modifiedon'] <> $latestModifiedOn)
			     $this->addErrorList($arrayToJs,false,$this->errorMsg[214]);
            
            // jika linked dr data lain, tdk boleh edit
			if(!isset($arr['_mnv_auto_update'])){ 
				if (isset($rs[0]['islinked']) && $rs[0]['islinked'] == 1)
					 $this->addErrorList($arrayToJs,false,$this->errorMsg[900]);
			}
        }
        
        if(isset($arr['code']) && !empty($arr['code'])){ 
            $code = $arr['code'];   
            $rs = $this->isValueExisted($pkey,'code',$code);	 
            if(empty($code)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
            }else if(count($rs) <> 0){ 
                $this->addErrorList($arrayToJs,false,'<strong>'.$code.'</strong>. '.$this->errorMsg['code'][2], array('code' => '280', 'pkey' => $rs[0]['pkey']));
            }
        }
		 
        // jika ad upload file dan size lebih besar dari quota
        // utk saat ini baru hitung yg S3
        if($this->totalUploadSize > 0){
            $security = new Security();
            $planType = $security->getUserPlanType();
            $diskUsage = $planType[0]['useddiskusage'] * 1024 * 1024;
            $maxDiskUsage = $planType[0]['maxdiskusage'] * 1024 * 1024;
            if($diskUsage + $this->totalUploadSize > $maxDiskUsage){
                   $this->addErrorList($arrayToJs,false,'<strong>'.$code.'</strong>. '.$this->errorMsg['limit'][6]);
            }  
        }
        
        return $arrayToJs;
    }
    
    function updateReadStatus($rs = array()){
          
        if (empty($_SESSION[$this->loginAdminSession]['id']))
            return;
         
        if (empty($rs))
            $rs = $this->rs;
            
        $userkey = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        if($rs[0]['createdby'] == $userkey)
            return; 
        
        try{
			 
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
									 
            $sql = 'select 
                        * 
                    from 
                        '.$this->tableReadLog.' 
                    where 
                        tablename = ' . $this->oDbCon->paramString($this->tableName) .' and 
                        refkey = ' . $this->oDbCon->paramString($rs[0]['pkey']) .' and
                        userkey =  ' . $this->oDbCon->paramString($userkey) .' 
                    ';
            
            $rsLog = $this->oDbCon->doQuery($sql);
            if (!empty($rsLog))
                return;
            
			$sql = ' INSERT INTO 
                    '.$this->tableReadLog.'  (
                        tablename,
                        refkey,
                        userkey
                    ) values ( 
                        '.$this->oDbCon->paramString($this->tableName).', 
                        '.$this->oDbCon->paramString($rs[0]['pkey']).',
                        '.$this->oDbCon->paramString($userkey).'
                    )';
			
			$this->oDbCon->execute($sql); 
			$this->oDbCon->endTrans();  
	
			//$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				   
    }
    
    function getReadStatus($rs){
        
        if (empty($rs)) return $rs; 
        
        $userkey = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        
        $arrKey = array();
        
        for($i=0;$i<count($rs);$i++)
            array_push($arrKey, $rs[$i]['pkey']); 
        
        $sql = 'select 
                        * 
                from 
                    '.$this->tableReadLog.' 
                where 
                    tablename = ' . $this->oDbCon->paramString($this->tableName) .' and 
                    refkey in ('.implode(",",$arrKey).') and
                    userkey =  ' . $this->oDbCon->paramString($userkey) .' 
                ';
         
        $rsLog = $this->oDbCon->doQuery($sql);
        
        
        $arrLog = array(); 
        for($i=0;$i<count($rsLog);$i++)
          array_push($arrLog, $rsLog[$i]['refkey']); 
        
        
        for($i=0;$i<count($rs);$i++){
            
            $rs[$i]['read'] = 0;
            if($rs[$i]['createdby'] == $userkey || in_array($rs[$i]['pkey'], $arrLog))
                $rs[$i]['read'] = 1;  
                
        }
        
        return $rs;
    }
    
    
    function hashFileName($filename){ 
        $path_parts  = pathinfo($filename);  
        $newFileName = md5($filename . rand(10,100)) .'.'. $path_parts['extension'];
        return $newFileName;
    }
    
     function addSeparator($string, $separator = ' '){
         
        $length = strlen($string);
        
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($string) > $dash_len)
        {
            $dash_str .= substr($string, 0, $dash_len) . $separator;
            $string = substr($string, $dash_len);
        }
        $dash_str .= $string;
        return $dash_str;
     }
    
     function generateStrongPassword($length = 9, $add_dashes = '', $available_sets = 'luds'){
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$*?';
        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];
        $password = str_shuffle($password);
        if(empty($add_dashes))
            return $password;
        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . $add_dashes;
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
    
    // ITEM CONVERSION UNIT FUNCTION 
    function updateConvMultiplier($id){
        $rs = $this->getDataRowById($id);
		$rsDetail = $this->getDetailById($id);
		$item = new Item();
		 
		for($i=0;$i<count($rsDetail);$i++){
			
            $convmultiplier = $item->getConvMultiplier($rsDetail[$i]['itemkey'],$rsDetail[$i]['unitkey']); 
            $qtyinbaseunit = $rsDetail[$i]['qty'] * $convmultiplier;
            
            $sql = 'update '.$this->tableNameDetail.' set unitconvmultiplier = ' .$convmultiplier.', qtyinbaseunit = '.$qtyinbaseunit.' where pkey = ' .$rsDetail[$i]['pkey'] ;
			$this->oDbCon->execute($sql);
            
        }
        
         
    } 
	
    function reCalculateConversionDetail($arrParam){
 
				$itemkey = $arrParam['hidItemKey'];
                $item = new Item();
			  
                $arrTransUnitKey = (isset($arrParam['selUnit']) && !empty($arrParam['selUnit'])) ? $arrParam['selUnit'] : array();
        
				$arrQty = $arrParam['qty'];

                $baseUnitKey = array();
                $unitConvMultiplier = array();
                $qtyInBaseUnit = array();
        
        
				for ($i=0;$i<count($itemkey);$i++){
					
					if (empty($itemkey[$i]))  
						continue;

                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    
                    $rsItem = $item->getDataRowById($itemkey[$i]);
                    
                    if (!isset($arrTransUnitKey[$i]) || empty($arrTransUnitKey[$i]))
                        $arrTransUnitKey[$i] = $rsItem[0]['baseunitkey'];
                    
                    $baseUnitKey[$i] = $rsItem[0]['baseunitkey'];
                    $unitConvMultiplier[$i] = $item->getConvMultiplier($itemkey[$i],$arrTransUnitKey[$i], $baseUnitKey[$i]); 
                    $qtyInBaseUnit[$i] = $unitConvMultiplier[$i]  * $qty; 
						 
				} 
				    
				
				$reCountResult = array(); 
				$reCountResult['baseUnitKey'] = $baseUnitKey;
				$reCountResult['unitConvMultiplier'] = $unitConvMultiplier;
				$reCountResult['qtyInBaseUnit'] = $qtyInBaseUnit;
				 
				return $reCountResult;
				
	}
    
    // ITEM CONVERSION UNIT FUNCTION 
    
    
	function getRefCode($refkey, $reftable){
		
		if (empty($reftable))
			return '';
			
		$sql = 'select coalesce(code,"") as code from ' .  $reftable .' where pkey = ' .$this->oDbCon->paramString($refkey)  ;	
		$rs = $this->oDbCon->doQuery($sql);	
	 
		return $rs[0]['code']; 	
			
	}
    
    function sayNumber($amount,$firstDecimal = true){
             
	    $amount = abs($amount);
        
        $whole = floor($amount);  
        $fraction = $amount - $whole;  
        $amount = $whole;

	    $getSpell = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

	    if ($amount < 12)
	        $temp =  $getSpell[$amount]." ";
	    elseif ($amount < 20)
	        $temp =  $this->sayNumber($amount - 10,false) . "belas ";
	    elseif ($amount < 100)
	        $temp =  $this->sayNumber($amount / 10,false) . "puluh " . $this->sayNumber(fmod($amount,10),false);
	    elseif ($amount < 200)
	        $temp =  "seratus " . $this->sayNumber($amount - 100,false);
	    elseif ($amount < 1000)
	        $temp =  $this->sayNumber($amount / 100,false) . "ratus " . $this->sayNumber(fmod($amount,100),false);
	    elseif ($amount < 2000)
	        $temp =  "seribu " . $this->sayNumber($amount - 1000,false);
	    elseif ($amount < 1000000)
	        $temp =  $this->sayNumber($amount / 1000,false) . "ribu " . $this->sayNumber(fmod($amount,1000),false);
	    elseif ($amount < 1000000000)
	        $temp =  $this->sayNumber($amount / 1000000,false) . "juta " . $this->sayNumber(fmod($amount,1000000),false);
	    elseif ($amount < 1000000000000)
	        $temp =  $this->sayNumber($amount / 1000000000,false) . "milyar " . $this->sayNumber(fmod($amount,1000000000),false);
	    elseif ($amount < 1000000000000000)
	        $temp =  $this->sayNumber($amount / 1000000000,false) . "trilyun " . $this->sayNumber(fmod($amount,1000000000000),false);

        if($firstDecimal && $fraction <> 0){  
           //paksa 2 decimal sementara
           $fraction = explode('.',strval(number_format($fraction,2)));  
           return $temp .' koma '. $this->sayNumber($fraction[1],false);
        }else{ 
	       return $temp;
        }
	}
    
    function getSignPerson($rs){ 
       
        $employee = new Employee();
        
        $arrSign = array();
        
        $user = '';
        if (!empty($rs[0]['createdby'])){ 
            $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
            $user = $rsEmployee[0]['name'];
        }
        array_push($arrSign, array($this->lang['created'] , $user) );
        
        
        $user = '';
        if (!empty($rs[0]['confirmedby'])){ 
            $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
            $user = $rsEmployee[0]['name'];
        }
        array_push($arrSign, array($this->lang['approved'] , $user) );
        
        array_push($arrSign, array($this->lang['received']) );
        
        
        return  $arrSign;
     
    }
	
    function generateSignLabel($rs, $arrSignLabel = array()){

        $arrSignLabel = (empty($arrSignLabel)) ? $this->getSignPerson($rs) : $arrSignLabel; 
        
        $html =' 
        <table cellpadding="4" class="sign">
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $html .='<td  class="sign-col"><strong>'.$arrSignLabel[$i][0].'</strong></td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        <tr>'; 
        for ($i=0;$i<count($arrSignLabel);$i++){
            $arrSignLabel[$i][1] = (isset($arrSignLabel[$i][1])) ? $arrSignLabel[$i][1] : '';
            $html .='<td  class="sign-name">'.$arrSignLabel[$i][1].'</td>';
            if ($i <> count($arrSignLabel) - 1)
                $html .= '<td class="sign-col-space"></td>';
        }
        $html .='</tr> 
        </table>' ;

        return  $html;
        
    }
    

    function camouflage($words,$digits = 4,$uncamoflage = false){
       
        $result = '';
        
        if ($uncamoflage){ 
            $result = substr($words,$digits);
        }else{ 
            $temp = $this->generateStrongPassword($digits);
            $result = $temp . $words;
        }
            
        return $result;
    }
    
    
    function uncamouflage($words,$digits = 4){
        return $this->camouflage($words,$digits,true); 
    }
	 

    function submitFormScript(){
        // will be deprecated
        // akan probblem kalo ad array of element
        
        $isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
        $targetKey = ($isQuickAdd) ? '$("#'.$_GET['targetID'] .' [name=\''.$_GET['keyElement'].'\']")' : '""';
        $targetValue = ($isQuickAdd) ? '$("#'.$_GET['targetID'] .' [name=\''.$_GET['valueElement'].'\']")' : '""';
        $revalidateField = (isset($_GET) && !empty($_GET['revalidateField']) ) ? $_GET['revalidateField'] : "false"; 
        $parentPanelId = (isset($_GET) && !empty($_GET['selectedPanelId'])) ? $_GET['selectedPanelId'] : '';
        $parentTitle = (isset($_GET) && !empty($_GET['title'])) ? $_GET['title'] : ''; 
        $valueDBField = (isset($_GET) && !empty($_GET['valueDBField'])) ? 'valueDBField:"'.$_GET['valueDBField'].'", '  : '';  
     
        $autoPrintURL = (isset($this->autoPrintURL)) ?  ' autoPrintURL : "' . $this->autoPrintURL . '"' : '';
            
        $return = ' 
            submitForm( e,
              {tabID : tabID },
              {parentPanelId : "'.$parentPanelId.'", parentTitle : "'.$parentTitle.'", '.$autoPrintURL.'  }, 
              {value:'.$targetValue.', '.$valueDBField.' key:'.$targetKey.', revalidateField: '.$revalidateField.'}
             ); 
        ';
        
        return $return; 
       
    }
    
    function validationFormSubmitParam(){
        
        $isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
        $targetKey = ($isQuickAdd) ? '$("#'.$_GET['targetID'] .' [name=\''.$_GET['keyElement'].'\']")' : '""';
        $targetValue = ($isQuickAdd) ? '$("#'.$_GET['targetID'] .' [name=\''.$_GET['valueElement'].'\']")' : '""';
        $revalidateField = (isset($_GET) && !empty($_GET['revalidateField']) ) ? $_GET['revalidateField'] : false; 
        $parentPanelId = (isset($_GET) && !empty($_GET['selectedPanelId'])) ? $_GET['selectedPanelId'] : '';
        $parentTitle = (isset($_GET) && !empty($_GET['title'])) ? $_GET['title'] : ''; 
        $valueDBField = (isset($_GET) && !empty($_GET['valueDBField'])) ?  $_GET['valueDBField'] : '';  // 'valueDBField:"'.$_GET['valueDBField'].'", ' // <-- salah kayanya, pas narik plaat mobil gagal di spk
     
        $autoPrintURL = (isset($this->autoPrintURL)) ? $this->autoPrintURL   : '';
            
        $arr = array();
        $arr['parentPanelId'] = $parentPanelId;
        $arr['parentTitle' ] = $parentTitle;
        $arr['value'] = $targetValue;
        $arr['key'] = $targetKey;
        $arr['revalidateField'] = $revalidateField;
        $arr['valueDBField'] = $valueDBField;
        $arr['autoPrintURL'] = $autoPrintURL;
        
       /* array_push($arr, array('parentPanelId' => $parentPanelId));
        array_push($arr, array('parentTitle' => $parentTitle));
        array_push($arr, array('value' => $targetValue));
        array_push($arr, array('key' => $targetKey));
        array_push($arr, array('revalidateField' => $revalidateField));
        array_push($arr, array('valueDBField' => $valueDBField));
        array_push($arr, array('autoPrintURL' => $autoPrintURL));*/
          
        return $arr;  
    }
    
    function showDataHistory($rs = array(),$year ='',$useHeader = true){
        
        
        if (empty($rs)) $rs = $this->rs;  
        if (empty($rs)) return '';
        
        // kalo gk dikirim parameter tahun, artinyacari sampe dapat
        $untilFound = (empty($year)) ? true : false;
            
        $saveLimit = 10; 
        $id = $rs[0]['pkey'];
        
        $rs = array();
        $year = (empty($year)) ? date('Y') : $year;
       
        // sementara karena g kbisa narik Obj dari tablekey
         $return = ''; 
         $useHeader = true;
         $rsHistory = array();
		
				
         while($year > 2015){     
            $rs = $this->generateDataHistory($id,$year); 
            
			if (!empty($rs)) // kalo gk di validasi, jd empty array nya
				$rsHistory = array_merge($rs,$rsHistory); 
			  
            if($rs[0]['actionkey'] == 11) break; 
            $year--;  
        }
        
        $rsJournal = $this->getJournal( $this->getTableKeyAndObj($this->tableName,array('key'))['key'] , $id);
        
        
        // default
        $footerMenuDefault = isset(THEME_SETTINGS['footermenu']) ? THEME_SETTINGS['footermenu']['value'] : ''; 
        $footerStickyDefault = isset(THEME_SETTINGS['footersticky']) ? THEME_SETTINGS['footersticky']['value']: ''; 
        
        $randId = 'footer'.rand(0,1000).time();    
		$return = ' <div style="clear:both;height:2em"></div>
                    <div id="'.$randId.'" class="footer-panel">  
                            <div class="flex">
                                <div class="footer-panel-menu user-select-none consume flex">
                                    <div rel-key="log" class="selected">'.$this->lang['log'].'</div>
                                    <div rel-key="journal">'.$this->lang['generalJournal'].'</div>
                                </div> 
                                <div class="icon-menu"><i class="pin fas fa-thumbtack"></i></div>  
                            </div>   
                            <div class="content log">'.$this->compileDataHistoryForAdminForm($rsHistory).'</div>
                            <div class="content journal hide">'.$this->compileGeneralJournalForAdminForm($id).'</div>  
                  </div> '; 
	
        $defaultSelectScript = '';
        if (!empty($footerMenuDefault)) 
            $defaultSelectScript .= '$("#'.$randId.'").find("[rel-key='.$footerMenuDefault.']").click();';
       
         if (!empty($footerStickyDefault)) 
            $defaultSelectScript .= '$("#'.$randId.'").find(".pin").click();';
       
        
        $script = '<script>
                    
                    $("#'.$randId.' .footer-panel-menu > div").click(function() {
                    
                      if($(this).hasClass("selected")) return;
                      
                      var panel = $(this).closest(".footer-panel");
                      var key = $(this).attr("rel-key");
                      
                      panel.find(".footer-panel-menu .selected").removeClass("selected"); 
                      $(this).addClass("selected");
                      panel.find(".content").addClass("hide"); 
                      panel.find("."+key).removeClass("hide"); 
                        
                      updateSaveButtonSticky($(this).closest(".ui-tabs-panel"));
                      updateThemeSettings("footermenu",key);
                    });
                    
                    $("#'.$randId.' .icon-menu i").click(function() {
                        $(this).toggleClass("selected");
                        $(this).closest(".footer-panel").toggleClass("sticky");
                    
                        updateSaveButtonSticky($(this).closest(".ui-tabs-panel"));
                        updateThemeSettings("footersticky", ($(this).hasClass("selected")) ? 1 : 0 );
                    });
                    
                    '.$defaultSelectScript.'
                    </script> '; 
	
        $script = $this->minimizeJavascriptSimple($script); 
        
        $return .= $script;
		
        return $return;
    }
    
    function compileGeneralJournalForAdminForm($id){
        $security = new Security();
    
        if (!$security->hasSecurityAccess($this->userkey,$security->getSecurityKey('GeneralJournal'),10)) return '';
        
        // jurnal 
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        $rsJournal = $this->getJournal($tablekey,$id); 
    
        $journal = '';
    
        if(!empty($rsJournal)){
            $journalDetail = '';
            $showHeader = true;
            $rsJournalCol = $this->reindexDetailCollections($rsJournal,'pkey'); 
            foreach($rsJournalCol as $glkey=>$GLRow){
                $rsJournal = $GLRow;
                
                $journalDetail .= '<div class="div-table journal-info">'; 
                
                if($showHeader){ 
                    $journalDetail .= '<div class="div-table-row col-header">';
                    $journalDetail .= '<div class="div-table-col"></div>';
                    $journalDetail .= '<div class="div-table-col coacode">'.$this->lang['code'].'</div>';
                    $journalDetail .= '<div class="div-table-col coaname">'.$this->lang['account'].'</div>';
                    $journalDetail .= '<div class="div-table-col value">'.$this->lang['debit'].'</div>';
                    $journalDetail .= '<div class="div-table-col value">'.$this->lang['credit'].'</div>';
                    $journalDetail .= '<div class="div-table-col refcode">'.$this->lang['reference'].'</div>';
                    $journalDetail .= '<div class="div-table-col desc">'.$this->lang['description'].'</div>';
                    $journalDetail .= '</div>';
                    
                    $showHeader = false;
                }
                
                foreach($rsJournal as $key=>$row){ 
                    $journalDetail .= '<div class="div-table-row">';
                    $journalDetail .= '<div class="div-table-col transcode">'.(($key == 0) ? $rsJournal[0]['code'] : '').'</div>';
                    $journalDetail .= '<div class="div-table-col coacode">'.$row['coacode'].'</div>';
                    $journalDetail .= '<div class="div-table-col coaname">'.$row['coaname'].'</div>';
                    $journalDetail .= '<div class="div-table-col value">'.$this->formatWithColor($row['debit']).'</div>';
                    $journalDetail .= '<div class="div-table-col value">'.$this->formatWithColor($row['credit']).'</div>';
                    $journalDetail .= '<div class="div-table-col refcode">'.$row['refcode'].'</div>';
                    $journalDetail .= '<div class="div-table-col desc">'.$row['trdesc'].'</div>';
                    $journalDetail .= '</div>';
                }
                $journalDetail .= '</div>';

            }
        
            
        $journal = $journalDetail;                 

    }
    
    return $journal;
    
    }
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value  
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
		
        return $sql;
        
    }
    
     function searchDataForAutoComplete($returnField, $searchOptions,$orderCriteria=''){ 
         
        $fieldname = (isset($searchOptions['field'])) ?  $searchOptions['field'] : '';
        $searchkey = (isset($searchOptions['key'])) ?  $searchOptions['key'] : '';
        $searchCriteria = (isset($searchOptions['criteria'])) ? $searchOptions['criteria'] : '';
             
        // update disini, karena  di generateDefaultQueryForAutoComplete repot, sering overwrite di class lain.
        if (is_array($returnField['value'])){
              $temp = array();

              // $obj->tableName.'.'. was temporary fix
              for($k=0;$k<count($returnField['value']);$k++){ 
                $colName = $returnField['value'][$k];
                 if( strpos($colName, '.') === false)  $colName =  $this->tableName.'.'.$colName;
                 array_push($temp,$colName); 
              }
            

             $returnField['value'] = 'concat('.implode('," - ",', $temp).')'; 
        } 
         
		$sql = $this->generateDefaultQueryForAutoComplete($returnField);
	
          
		if(!empty($fieldname)){  
            if (is_array($fieldname)){
                $tempArray = array();
                
                //$obj->tableName.'.' as temporary fixed
                for($i=0;$i<count($fieldname);$i++)
                    array_push($tempArray,$this->tableName.'.'. $this->oDbCon->paramOrder($fieldname[$i]) . ' like '. $this->oDbCon->paramString('%'.$searchkey.'%') );
                
                $tempSql = implode(' or ' , $tempArray);
            }else{ 
                $tempSql =  $this->oDbCon->paramOrder($fieldname) .' like '. $this->oDbCon->paramString('%'.$searchkey.'%'); 
            } 
            
            $sql .= ' and (' . $tempSql . ')'; 
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
        $sql .= $this->getCreatedByCriteria();
         
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;
		 
         //$this->setLog($sql,true);
         $rs = $this->oDbCon->doQuery($sql);	
         for($i=0;$i<count($rs);$i++) 
            $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
             
         return $rs;
	} 
    
    function getCargoType($id = ''){
        
        if (is_array($id)) 
            $criteria =  ' and pkey in ('.$this->oDbCon->paramString(implode(',',$id)).')'; 
        else 
            $criteria = (empty($id)) ? '' : ' and pkey = ' . $this->oDbCon->paramString($id);
         
        $sql = 'select * from cargo_type where 1=1 '.$criteria;
        
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }

     function validateAllowedStatus($rs){
        
        $sql = 'select minstatustoprint from '.$this->tablekey.' where tablename = ' .$this->oDbCon->paramString($this->tableName) ; 
        
        $rsStatus  = $this->oDbCon->doQuery($sql); 
        $minStatus = $rsStatus[0]['minstatustoprint'];
            
        if ( $rs[0]['statuskey'] < $minStatus){
            
            $rsStatus = $this->getStatusById ($minStatus);
            
            echo '<div><strong>'.$rs[0]['code']. '</strong>. '.$this->errorMsg['print'][2].' '.$this->lang['minimumStatusRequired'].': '. $rsStatus[0]['status'].'.</div>';
            die;
        }
          
           
    }
    
    function hideOnDisabled($allowedStatusForEdit = array()){
        
      if (empty($allowedStatusForEdit))
        $allowedStatusForEdit = $this->allowedStatusForEdit;
         
        $rs = $this->rs;
        
        if (empty($rs) || empty($allowedStatusForEdit))
            return; 
        
        if (!in_array($rs[0]['statuskey'],$allowedStatusForEdit))
            return 'display-none';
    }
    
    function getDuplicatedValue($arr,$rsRef=array(),$field= array()){
          
        $arrDetail = array();
        $arrDuplicate = array();
        
		for($i=0;$i<count($arr);$i++) { 
             
            if (in_array($arr[$i],$arrDetail)){   
                array_push($arrDuplicate, $arr[$i]);
            }else{ 
                array_push($arrDetail, $arr[$i]);
            }
            
        }
        
         if(count($arrDuplicate) > 0 ) { 
             $arrDuplicateData = array();
             $rsRef = array_column($rsRef,null,'pkey');
             foreach($arrDuplicate as $key=>$row) {
                     
                 $arrDuplicateData[$key] = array();
                 
                 foreach($field as $fieldRow) {
                     $arrDuplicateData[$key][$fieldRow] = $rsRef[$row][$fieldRow];
                 }
             } 
                     
        }
        
        
         return array('key' => $arrDuplicate, 'data' => $arrDuplicateData); 
    }
    
    function getDataRowType($dataRow){
        $optiontemp = (isset($dataRow[1])) ? $dataRow[1] : '';  

        if (is_array($optiontemp)) 
            $dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
        else
            $dataType = $optiontemp;
        
        return $dataType;
    }
    
     function updateMnvFileName(&$arrParam, $arrData = '',$ctr=0){
        
        // khusus update STORAGE
         
        if ($ctr > 10 ) return; // buat jaga2 aj
          
        // hanya boelh sekali
        // karena ad struktur data yang level kedua, array dataDetailnya bkn  2 dimensi
        if($ctr == 0 && $arrData == '') $arrData = $this->arrData;
       
        $ctr++; 
        
        // utk array, harus di reindexing
        
        foreach($arrData as $dataRow){
            
            if(isset($dataRow[1]['dataDetail'])){
                foreach($dataRow[1]['dataDetail'] as $detailRow) { 
                    $newDataSet = (is_array($detailRow) && isset($detailRow['dataset'])) ?  $detailRow['dataset'] : '';
                    $this->updateMnvFileName($arrParam,$newDataSet,$ctr);   
                }
            }else{
                
                 $dataType = $this->getDataRowType($dataRow);
                 
                 if(!in_array($dataType, array('image','file'))) continue; 
                    
                    // TEST, utk multiple, harus test ulang utk yg single file
                     if(isset($arrParam['_mnv-api']) && $arrParam['_mnv-api'] == 1){
                          
                            if(is_array( $arrParam[$dataRow[0]])){
                                $temp = array('name' => array());
                                
                                foreach( $arrParam[$dataRow[0]] as $detailRow)
                                  array_push($temp['name'], $detailRow);  
                            }else{
                                $temp = array('name' => $arrParam[$dataRow[0]]); 
                            }

                         $arrParam[$dataRow[0]] = $temp;
                     }


                         // kalo ad nilai update, dari edit form   
                        // kalo pake API, harusnya !isset($arrParam[$dataRow[0]]['name']) udah gk kepake
                        if(!isset($arrParam[$dataRow[0]]['name']) || !is_array($arrParam[$dataRow[0]]['name'])){ 
 
                             if(empty($arrParam[$dataRow[0]]['name'])){  
                                  $arrParam[$dataRow[0]] =  array('name' =>  (empty($arrParam[$dataRow[0]]['name'])) ? $arrParam['hid'.$dataRow[0]] : $arrParam[$dataRow[0]]['name'] );    
                             }

                            //$arrParam[$dataRow[0]] = array('name' => '');
                            //$arrParam[$dataRow[0]]['name'] =  (empty($arrParam[$dataRow[0]]['name'])) ? $arrParam['hid'.$dataRow[0]] : $arrParam[$dataRow[0]]['name'];

                        }else{
 

                            $total = count($arrParam[$dataRow[0]]['name']); // klao gk pake index name, jmlnya beda....

                            // utk array, harus di reindexing
                            $varTemp = array();
                            for($i=0;$i<$total;$i++){

                                array_push($varTemp,array(
                                                            'name' => (empty($arrParam[$dataRow[0]]['name'][$i])) ? $arrParam['hid'.$dataRow[0]][$i] : $arrParam[$dataRow[0]]['name'][$i],
                                                            'type' => $arrParam[$dataRow[0]]['type'][$i],
                                                            'tmp_name' => $arrParam[$dataRow[0]]['tmp_name'][$i],
                                                            'error' => $arrParam[$dataRow[0]]['error'][$i],
                                                            'size' => $arrParam[$dataRow[0]]['size'][$i],
                                                        ));

                                $this->totalUploadSize +=  $arrParam[$dataRow[0]]['size'][$i];
                            }

                            $arrParam[$dataRow[0]] = $varTemp;

                        }
           
                
                 
            }
            
        }
        
        
    }
    
    
    function normalizeParameter($arrParam,$trim = false){
		
        // khusus dari STORAGE
         
          if($this->useStorage('S3')){ 
                $this->updateMnvFileName($arrParam);
          }else{

                    // kalo ad yg jenis file
                    // hidTotalRows udah gk perlu harus nya
                    // upload queue disini saja langsung
                    // nanti perlu cek kalo dari API, masalah gk .. 
                    if(!isset($arrParam['_mnv-api'])){
                        if(isset($this->arrData['pkey'][1]['dataDetail'])){
                            $arrDetails = $this->arrData['pkey'][1]['dataDetail'];


                            foreach($arrDetails as $detailRow){
                                if( isset($detailRow['datatype']) && in_array($detailRow['datatype'], array('image','file'))){


                                  $fileParam = $detailRow['fileName'];
                                  $tokenVar = $detailRow['token'];
                                  $hidKeyVar = $detailRow['dataset']['pkey'][0];
                                  $tableName = $detailRow['tableName'];
                                  $uploadFolder = (isset($detailRow['uploadFolder'])) ? $detailRow['uploadFolder'] : $this->uploadFolder; 

                                  // kalo edit, ambil data yg ada 
                                  $arrUsedPkey=array();
                                  if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){
                                      $rsExistingFile = $this->getFileDetail($arrParam['hidId'],$tableName );
                                      $arrUsedPkey = array_column($rsExistingFile,'pkey');
                                  }	

                                  $arrParam[$fileParam] = explode(',',$arrParam[$fileParam] ?? '');
                                  $token = $arrParam[$tokenVar];

                                  // set ulang tokennya
                                  $arrParam[$tokenVar] = array();
                                  foreach($arrParam[$fileParam] as $key=>$fileRow){   
                                      $arrParam[$hidKeyVar][$key] = (isset($arrUsedPkey[$key])) ? $arrUsedPkey[$key] : 0; // pake existing pkey jiak sudah ada
                                      $arrParam[$tokenVar][$key] = $token;

                                      // upload queue 
                                        $arrUpload = array();
                                        $arrUpload['uploadFolder'] = $uploadFolder;
                                        $arrUpload['token'] = $token;
                                        $arrUpload['fileName'] = $fileRow; 

                                        array_push($this->uploadQueue, $arrUpload);  
                                  }

                                }
                            }	
                        }
                    }

          }
        
		//$this->setLog($this->uploadQueue,true); 
		
        
        $isImport  = (isset($arrParam['_isImport_'])) ? true : false; 
        if ($isImport) { 
            // remove all details 
            $this->arrData['pkey'] = array('pkey');  
            $trim = true;
        }
        
        if ($trim)
            $this->trimData($arrParam); 
        
//		$this->setLog($arrParam,true);
        return $arrParam;
    }
        
    
    function getBaseClass(){
        return $this;
    }
    function updateContactPerson($id, $arrParam,$tableName = ''){
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        $sql = 'delete from '.$this->tableContact .' where reftable = ' . $this->oDbCon->paramString($tableName).' and refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);  
        
        $arrCPName = $arrParam['cpName'];
        $arrCPPosition = $arrParam['cpPosition'];
        $arrCPPhone = $arrParam['cpPhone'];
        
        for ($i=0;$i<count($arrCPName);$i++){
            if (empty($arrCPName[$i])) continue;
                
            $sql = 'insert into 
                        '.$this->tableContact .'(
                            refkey,
                            reftable,
                            name,
                            position,
                            phone
                    ) values  ( 
                            ' . $this->oDbCon->paramString($id).', 
                            ' . $this->oDbCon->paramString($tableName).', 
                            ' . $this->oDbCon->paramString($arrCPName[$i]).', 
                            ' . $this->oDbCon->paramString($arrCPPosition[$i]).', 
                            ' . $this->oDbCon->paramString($arrCPPhone[$i]).'
                    ) ';
            
            $this->oDbCon->execute($sql);   
        } 
    }
     
    function getLatestContactPersonTemplate($arrColumn = array('position'), $tableName = ''){
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        $columnName = implode(',',$arrColumn);
        
        
        
        $sql = 'select 
                   '.$columnName.'
                from 
                    '. $this->tableContact.' 
                where 
                    reftable = '.$this->oDbCon->paramString($tableName).'  and
                    position <> "" and
                    refkey = ( 
                            select pkey 
                            from '.$tableName.'  
                            order by pkey desc limit 1
                        )
                order by position asc';
        
        $rs =   $this->oDbCon->doQuery($sql);   
        return $rs;
    }
    
    function  getContactPerson($id,$tableName = ''){
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        $sql = 'select * from '. $this->tableContact.' where reftable = '.$this->oDbCon->paramString($tableName).' and refkey = '.$this->oDbCon->paramString($id);
        return $this->oDbCon->doQuery($sql);
        
    }
      
          
    function contactPersonPlugin($rsContactPerson = array(), $options = array()){
        
        $default = array( 
                      'templateName' => 'contact-row-template',   
                      'field' => array(
                                        array('title' => ucwords($this->lang['position']), 'selector' => 'cpPosition[]','data' => 'position','style' => 'width:130px;'),
                                        array('title' => ucwords($this->lang['name']), 'selector' => 'cpName[]','data' => 'name'),
                                        array('title' => ucwords($this->lang['phone']), 'selector' => 'cpPhone[]','data' => 'phone','style' => 'width:130px;'),
                                ),
                      'rememberLatest' => true,
                      'allowedStatusForEdit' => $this->allowedStatusForEdit
                    );
   
        $args = (empty($options)) ? $default : array_merge($default, $options);     
        
        
        if ($args['rememberLatest'] && empty($rsContactPerson)) 
            $rsContactPerson = $this->getLatestContactPersonTemplate();
        
        
        $templateName = $args['templateName']; 
        $field = $args['field'];
        
        $result ='
            <div class="div-table transaction-detail" style="width:100%">
                 <div class="div-table-row">';

        for($k=0;$k<count($field);$k++){
            $style = (isset($field[$k]['style']) && !empty($field[$k]['style'])) ?  $field[$k]['style'] : '';
            $result .=' <div class="div-table-col detail-col-header" style="'.$style.' border:0;">'. $field[$k]['title'] .'</div> ';
        }
        
        $result .='<div class="div-table-col  '.$this->hideOnDisabled().'" style="width:20px;"></div>';
        $result .= '</div> ';  // end of row
           
          $totalRows = count($rsContactPerson); 

          for ($i=0;$i<=$totalRows; $i++){  

                $class =  'transaction-detail-row';
                $style = '';
                $overwrite = true;
                $etc = ''; 

                $statusStyle = '';
                $detail = '';
                 
                if ($i == $totalRows ){
                    $class = ' row-template '.$templateName;
                    $style = 'style="display:none"';
                    $overwrite = false;
                    $etc = 'disabled="disabled"';   
                } else {    
                    $_POST['hidContactPersonDetailKey[]'] = (isset($rsContactPerson[$i]['pkey'])) ?  $rsContactPerson[$i]['pkey'] : 0;
                    for($k=0;$k<count($field);$k++) 
                        $_POST[$field[$k]['selector']] = (isset($rsContactPerson[$i][$field[$k]['data']])) ? $rsContactPerson[$i][$field[$k]['data']] : '';  
                }

            $result .=' <div class="div-table-row  '.$class.'" '.$style.' >';

            for($k=0;$k<count($field);$k++) 
                $result .='<div class="div-table-col detail-col-detail">'.$this->inputText($field[$k]['selector'],array('overwritePost' => $overwrite, 'allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc' =>  $etc )).'</div>';

            $result .=' <div class="div-table-col detail-col-detail '.$this->hideOnDisabled().'">
                            '.$this->inputHidden('hidContactPersonDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)).'
                            '.$this->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')).'
                        </div>
             </div> ';  // end of row
        }
 
        $result .= '</div> ';  // end of table
        $result .='
           <div style="clear:both; height:1em;"></div>  
            <div class="div-table transaction-detail" style="width:100%;"> 
                <div class="div-table-row">
                    <div class="div-table-col detail-col-detail">'.$this->inputButton('btnAddContact',ucwords($this->lang['addRows']), array('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc'=> 'onClick = addNewTemplateRow("'.$templateName.'")', 'class' => 'btn btn-primary btn-second-tone')).'</div>  
                </div>
            </div>  
        ';
        
        if (empty($rsContactPerson))
         $result .='<script>  $(function() {addNewTemplateRow("'.$templateName.'");}); </script>  '; 
        
        return $result;
    }
     
         
     function multipleAddressPlugin($rsAddress = array(), $options = array()){
           
           $targetID = $_SESSION['targetID']++;
           $templateName = 'address-row-template';
         
           $default = array( 
                      'templateName' => $templateName,   
                      'field' => array( 
                                        array('title' => ucwords($this->lang['name']), 
                                              'element' => array( array('selector' => 'maName[]','data' => 'name')),
                                              'style' =>array('width' => '150px')
                                             ),
                                        array('title' => ucwords($this->lang['address']), 
                                              'element' => array(array('selector' => 'maAddress[]','data' => 'address', 'type' => 'textarea')), 
                                             ) ,
                                        array('title' => ucwords($this->lang['location']),  
                                              'element' => array(
                                                                    array('selector' => 'maLocationName[]','data' => 'name', 'datalink' => 'locationkey','obj'=> new Location()),
                                                                    array('selector' => 'maHidLocationKey[]','data' => 'locationkey', 'type' => 'hidden'),   
                                                                )
                                             ) 
                                ),
                      'rememberLatest' => false, 
                      'layoutStyle' => 2,
                      'allowedStatusForEdit' => $this->allowedStatusForEdit,
                      'script' => '<script>   
                                        $(function() {
                                                var objAndValueForDetailCityAutoComplete = {};     
                                                objAndValue = new Array;
                                                objAndValue.push({object:\'maHidLocationKey[]\', value :\'pkey\'});  
                                                objAndValueForDetailCityAutoComplete["'.$targetID.'"] = objAndValue;   

                                                bindAutoCompleteForTransactionDetail(\'maLocationName[]\',objAndValueForDetailCityAutoComplete["'.$targetID.'"],\'ajax-location.php?action=searchData\');  

                                                $("#'.$targetID.' [name=btnAddRowsPlugin]").on(\'click\', function() {  
                                                    var newRow = addNewTemplateRow("'.$templateName.'"); 
                                                    bindAutoCompleteForTransactionDetail(\'maLocationName[]\',objAndValueForDetailCityAutoComplete["'.$targetID.'"],\'ajax-location.php?action=searchData\');   
                                                }); 
                                            } 
                                        ); 
                                </script> '
                    ); 
   
        $args = (empty($options)) ? $default : array_merge($default, $options);     
         
        $result = $this->generatePluginTable($targetID,$rsAddress, $args);
         
         
        return $result;
     }
    
     function costPlugin($rsCost = array(), $options = array()){
           
        $targetID = $_SESSION['targetID']++;
        $moduleCost = (!empty($options['module'])) ? '&moduleCost='.$options['module'] : '';
        $chargePer = (!empty($options['chargePer'])) ? '&chargePer='.$options['chargePer'] : '';
        $templateName = 'cost-row-template';
         
        $default = array( 
                      'templateName' => $templateName,   
                      'field' => array(
                                    /*    array('title' => ucwords($this->lang['qty']), 
                                              'element' => array( array('selector' => 'costQty[]','data' => 'position', 'type' => 'number')), 
                                              'style' => array('width' => '60px', 'text-align' => 'right')
                                             ), */
                                        array('title' => ucwords($this->lang['nameOfCost']), 
                                              'element' => array(
                                                                    array('selector' => 'costName[]','data' => 'name', 'datalink' => 'costkey','obj'=>new Service(1)),
                                                                    array('selector' => 'hidCostKey[]','data' => 'costkey', 'type' => 'hidden'),   
                                                                )
                                             ),
                                        array('title' => ucwords($this->lang['cost']), 
                                              'element' => array(array('selector' => 'costAmount[]','data' => 'price', 'type' => 'number')),
                                              'style' => array('width' => '130px', 'text-align' => 'right')
                                             ) 
                                ),
                      'rememberLatest' => true, 
                      'layoutStyle' => 1,
                      'allowedStatusForEdit' => $this->allowedStatusForEdit,
                      'script' => '<script>   
                                        $(function() {
                                                var objAndValueForDetailCostAutoComplete = {};     
                                                objAndValue = new Array;
                                                objAndValue.push({object:\'hidCostKey[]\', value :\'pkey\'});  
                                                objAndValueForDetailCostAutoComplete["'.$targetID.'"] = objAndValue;   

                                                bindAutoCompleteForTransactionDetail(\'costName[]\',objAndValueForDetailCostAutoComplete["'.$targetID.'"],\'ajax-item.php?action=searchData&itemtype=2&serviceCost=1'.$moduleCost.$chargePer.'\');  

                                                $("#'.$targetID.' [name=btnAddRowsPlugin]").on(\'click\', function() {  
                                                    var newRow = addNewTemplateRow("'.$templateName.'"); 
                                                    bindAutoCompleteForTransactionDetail(\'costName[]\',objAndValueForDetailCostAutoComplete["'.$targetID.'"],\'ajax-item.php?action=searchData&itemtype=2&serviceCost=1'.$moduleCost.$chargePer.'\');   
                                                }); 
                                            } 
                                        ); 
                                </script> '
                    );
   
        $args = (empty($options)) ? $default : array_merge($default, $options);     
         
        $result = $this->generatePluginTable($targetID,$rsCost, $args); 
         
        return $result;
    }
    
    
    function generatePluginTable($targetID, $rs,$args){
                      
        $templateName = $args['templateName']; 
        $field = $args['field'];
          
        // generating header    
        $arrTitle = array();
        $arrHeader = array();
        $arrHeader['style'] = array();
        $arrHeader['title'] = array();

        for($k=0;$k<count($field);$k++){
          $style = '';

          if (isset($field[$k]['style']) && !empty($field[$k]['style'])) { 
            $styleList = $field[$k]['style'];

            $arrStyle = array();
            foreach ($styleList as $key => $value) 
                 array_push($arrStyle,  $key.':'.$value);  


            $style = implode(';', $arrStyle);
            $style = (empty($style)) ? '' : $style . ';' ;
          }

          array_push($arrHeader['style'],  $style); 
          array_push($arrHeader['title'], $field[$k]['title']);  
        }
         
        
        // generating detail
          $arrDetail = array();
          $totalRows = count($rs); 

          for ($i=0;$i<=$totalRows; $i++){  

                $class =  'transaction-detail-row';
                $style = '';
                $overwrite = true;
                $etc = ''; 

                $statusStyle = '';
                $detail = '';

                if ($i == $totalRows ){ 
                    $overwrite = false;
                    $etc = 'disabled="disabled"';  
                } else {    
                    for($k=0;$k<count($field);$k++) { 
                        $arrElement = $field[$k]['element']; 
                        for($ctr=0;$ctr<count($arrElement);$ctr++){   
                            
                             if (isset($arrElement[$ctr]['datalink']) && !empty($arrElement[$ctr]['datalink'])){ 
                                $rsDataLink = $arrElement[$ctr]['obj']->getDataRowById($rs[$i][$arrElement[$ctr]['datalink']]);    
                                $value = $rsDataLink[0]['name'];  
                             } else{ 
                                 // cek kalo kolomnya ada ap gk
                                 if (!isset($rs[$i][$arrElement[$ctr]['data']]) || empty($rs[$i][$arrElement[$ctr]['data']]) )
                                    continue; 
 
                                $value = (isset($rs[$i][$arrElement[$ctr]['data']])) ? $rs[$i][$arrElement[$ctr]['data']] : '';   
                             }
 
                             
                              if (isset($arrElement[$ctr]['type'])){
                                  if ($arrElement[$ctr]['type'] == 'number')
                                        $value = $this->formatNumber($value);
                              }
                            
                            $_POST[$arrElement[$ctr]['selector']] = $value;
                        } 
                    }
                }
 
            // start generating field element 
            $arrTemp = array();
            $arrTemp['input'] = array();
              
              for($k=0;$k<count($field);$k++) {
                $arrElement = $field[$k]['element']; 
                
                $input = '';
                
                for($ctr=0;$ctr<count($arrElement);$ctr++){
                    
                    $arrElement[$ctr]['type'] = (isset($arrElement[$ctr]['type'])) ? $arrElement[$ctr]['type'] : '';
                    switch($arrElement[$ctr]['type']){
                        case 'number' : 
                                $input .= $this->inputNumber($arrElement[$ctr]['selector'],array('overwritePost' => $overwrite, 'allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc' => 'style="text-align:right;"'. $etc ));     
                                break;
                        case 'textarea' : 
                                $input .= $this->inputTextArea($arrElement[$ctr]['selector'],array('overwritePost' => $overwrite, 'allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc' => 'style="height:8em"'. $etc ));     
                                break; 
                        case 'hidden' : 
                                 $input .= $this->inputHidden($arrElement[$ctr]['selector'],array('overwritePost' => $overwrite, 'allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc' => 'style="text-align:right;"'. $etc ));     
                                break;
                            
                        default : $input .= $this->inputText($arrElement[$ctr]['selector'],array('overwritePost' => $overwrite, 'allowedStatusForEdit' => $args['allowedStatusForEdit'], 'etc' => $etc ));    
                    }  
                } 
                    
                array_push( $arrTemp['input'], $input); 
            }
               
          
            array_push($arrDetail, $arrTemp);
              
        } // end of each row
          
        $result ='
            <div id="'.$targetID.'" >
            <div class="div-table transaction-detail" style="width:100%">';
        
         switch($args['layoutStyle']){

            case '2' :   // utk dummy css, agar baris ke 2 ada odd/even stylenya
                        $result .= '<div class="div-table-row" style="display:none">'; 
                        $result .=' <div class="div-table-col"></div> '; 
                        $result .=' <div class="div-table-col"></div> '; 
                        $result .='<div class="div-table-col"></div>'; 
                        $result .= '</div>';
                 
                        for ($i=0;$i<=$totalRows; $i++){  
                            $rows = ''; 
                            if ($i == $totalRows ){
                                $class = $templateName;
                                $style = 'style="display:none"'; 
                            } else{
                                $class =  'transaction-detail-row';
                                $style = '';
                            }
                                
                            $rows .= '<div class="div-table-row '.$class.'" '.$style.'>'; 
                            $rows .='<div class="div-table-col detail-col-detail" style="vertical-align:top; width: 15em"><strong>'.$arrHeader['title'][0] .'</strong><br>'.$arrDetail[$i]['input'][0].'</div>';
                      
                            $temp = '';
                            for($ctr=1;$ctr<count($field);$ctr++)
                                $temp .='<div style="padding-bottom:1em"><strong>'.$arrHeader['title'][$ctr] .'</strong><br>'.$arrDetail[$i]['input'][$ctr].'</div>'; 

                            $rows .='<div class="div-table-col detail-col-detail" style="vertical-align:top;">'.$temp.'</div>'; 
                            $rows .=' <div class="div-table-col detail-col-detail '.$this->hideOnDisabled().'" style="vertical-align:top; text-align:center">'.$this->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')).'</div>';  // end of row 
                            $rows .= '</div>';
                           
                           $result .= $rows;
                        }
                  
                        break;

            default :   $result .= '<div class="div-table-row" >'; 
                        for($k=0;$k<count($field);$k++)
                            $result .=' <div class="div-table-col detail-col-header" style=" '.$arrHeader['style'][$k].' border:0;">'. $arrHeader['title'][$k] .'</div> '; 
                        $result .='<div class="div-table-col detail-col-header '.$this->hideOnDisabled().'" style="width:20px; border:0;"></div>'; 
                        $result .= '</div>';
                  
                       for ($i=0;$i<=$totalRows; $i++){  
                            $rows = ''; 
                            if ($i == $totalRows ){
                                $class = $templateName;
                                $style = 'style="display:none"'; 
                            } else{
                                $class =  'transaction-detail-row';
                                $style = '';
                            }
                                
                            $rows .= '<div class="div-table-row '.$class.'" '.$style.'>'; 
                            for($ctr=0;$ctr<count($field);$ctr++)
                                $rows .='<div class="div-table-col detail-col-detail" style="vertical-align:top">'.$arrDetail[$i]['input'][$ctr].'</div>'; 

                            $rows .=' <div class="div-table-col detail-col-detail '.$this->hideOnDisabled().'" style="vertical-align:top;">'.$this->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0"')).'</div>';  // end of row 
                            $rows .= '</div>';
                           
                           $result .= $rows;
                       }
                        

        }
          
        
        $result .= '</div> ';  // end of table
 

         $result .='
               <div style="clear:both; height:1em;"></div>  
                <div class="div-table transaction-detail" style="width:100%;"> 
                    <div class="div-table-row">
                        <div class="div-table-col detail-col-detail">'.$this->inputButton('btnAddRowsPlugin',ucwords($this->lang['addRows']), array('allowedStatusForEdit' => $args['allowedStatusForEdit'], 'class' => 'btn btn-primary btn-second-tone')).'</div>  
                    </div>
                </div>  
            </div>    
        ';
          
        
         $result .= (empty($rs)) ? ' <script> $(function() {addNewTemplateRow("'.$templateName.'")} ); </script> 
                                  ' : '';
        
         $result .= $args['script']; 
        
        return $result;
    }
    
    
    function updateCost($id, $arrParam ){
        
        $sql = 'delete from '.$this->tableCost .' where refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);  
        
        if (!isset($arrParam['hidCostKey']))
            return;
        
        $arrCostKey = $arrParam['hidCostKey'];
        $arrCostValue = $arrParam['costAmount']; 
        
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        for ($i=0;$i<count($arrCostKey);$i++){
            if (empty($arrCostKey[$i]) || empty($arrCostValue[$i])) continue;
                
            $sql = 'insert into 
                        '.$this->tableCost .'(
                            refkey,
                            reftabletype,
                            costkey, 
                            price
                    ) values  ( 
                            ' . $this->oDbCon->paramString($id).',  
                            ' . $this->oDbCon->paramString($rsKey['key']).',  
                            ' . $this->oDbCon->paramString($arrCostKey[$i]).', 
                            ' . $this->oDbCon->paramString($this->unFormatNumber($arrCostValue[$i])).' 
                    ) ';
            
            $this->oDbCon->execute($sql);   
        } 
    }
/*    
    function getCost($id,$tableName = ''){ 
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        $sql = 'select * from '. $this->tableCost.' where  reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and  refkey = '.$this->oDbCon->paramString($id);
        return $this->oDbCon->doQuery($sql); 
    }*/
       
    
    function updateMultipleAddres($id, $arrParam,$tableName = '' ){
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $sql = 'delete from '.$this->tableMultipleAddress .' where refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);  
        
        if (!isset($arrParam['maName']))
            return;
        
        $arrName = $arrParam['maName'];
        $arrAddress = $arrParam['maAddress']; 
        $arrLocationKey= $arrParam['maHidLocationKey']; 
        $arrZipcode= $arrParam['maZipCode']; 
        $arrPIC= $arrParam['maPIC']; 
        $arrPhone= $arrParam['maPhone']; 
        
        for ($i=0;$i<count($arrName);$i++){
            if (empty($arrName[$i])) continue;
                
            $sql = 'insert into 
                        '.$this->tableMultipleAddress .'(
                            refkey,
                            reftable,
                            typekey,
                            name,
                            address,
                            zipcode,
                            locationkey,
                            pic,
                            phone
                    ) values  ( 
                            ' . $this->oDbCon->paramString($id).',  
                            ' . $this->oDbCon->paramString($this->tableName).',  
                            ' . $this->oDbCon->paramString($arrParam['maTypeKey']).',  
                            ' . $this->oDbCon->paramString($arrName[$i]).',
                            ' . $this->oDbCon->paramString($arrAddress[$i]).' ,
                            ' . $this->oDbCon->paramString($arrZipcode[$i]).' ,
                            ' . $this->oDbCon->paramString($arrLocationKey[$i]).' ,
                            ' . $this->oDbCon->paramString($arrPIC[$i]).' ,
                            ' . $this->oDbCon->paramString($arrPhone[$i]).' 
                    ) ';
            
            $this->oDbCon->execute($sql);   
        } 
    }
    
    function getMultipleAddress($id,$typekey = 1, $tableName = '', $criteria = ''){  
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $sql = 'select * from '. $this->tableMultipleAddress.' where  reftable = '.$this->oDbCon->paramString($tableName).' and refkey = '.$this->oDbCon->paramString($id) .' and typekey = ' . $this->oDbCon->paramString($typekey) ;
		
		if(!empty($criteria))
			$sql .= ' ' . $criteria;
		
		
        return $this->oDbCon->doQuery($sql); 
    }
    
    function getAvailableAddress($pkey,$typekey = array()){
        
        $sql = '';
        
        if (in_array(0,$typekey)){
            $sql = '   select 
                        0 as typekey, 
                        \'Alamat Korespondensi\' as name,
                        address,
                        0 as locationkey,
                        \'\' as locationname
                    from 
                        '.$this->tableName.' 
                        where pkey = '.$this->oDbCon->paramString($pkey).' 
                    UNION ';
        }
        
        $sql .= ' 
                select 
                    '.$this->tableMultipleAddress.'.typekey,
                    '.$this->tableMultipleAddress.'.name,
                    '.$this->tableMultipleAddress.'.address,
                    '.$this->tableMultipleAddress.'.locationkey,
                    '.$this->tableLocation.'.name as locationname 
                from 
                    '.$this->tableMultipleAddress.',
                    '.$this->tableLocation.'  
                where 
                    '.$this->tableMultipleAddress.'.reftable = \''.$this->tableName.'\' and 
                    '.$this->tableMultipleAddress.'.typekey in ('.implode(',',$this->oDbCon->paramString($typekey)).') and
                    '.$this->tableMultipleAddress.'.locationkey = '.$this->tableLocation.'.pkey and
                    '.$this->tableMultipleAddress.'.refkey = (select pkey from  '.$this->tableName.' where pkey = '.$this->oDbCon->paramString($pkey).')
        ';
          
        return $this->oDbCon->doQuery($sql);   
    }
     
    
    function getCompanyCriteria($tableName = ''){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
         if (!$this->isMultiCompany($tableName) || !$this->filterDataByCompany)
             return '';
        
        $employee = new Employee();
        $rsCompany = array_column($employee->getAccessCompany($this->userkey),'companykey'); 
        $companyKey = implode(',', $rsCompany);
         
        //or '.$this->tableName. '.pkey  = ' .  $this->oDbCon->paramString($this->userkey)
        $criteria = '';
        if (!empty($rsCompany)){ 
            $employeeCriteria = ($tableName == 'employee') ? ' or '.$tableName. '.pkey  = ' .  $this->oDbCon->paramString($this->userkey) : '';
            $criteria = ' and ( '.$tableName.'.companykey in ('.$companyKey.') '.$employeeCriteria.')'; 
        }
        
        return $criteria;
    }
     
    function useWarehousePrivileges(){
        // buat cek setiap MODUL pake akses gudang gk
        $sql = 'select pkey from '.$this->tableSecurityObject.' where usewarehouseprivileges = 1';
        $rs = $this->oDbCon->doQuery($sql);
        
        return (!empty($rs)) ? true : false;
        
    }
       
    function useCustomerPrivileges(){  
        // buat cek setiap USER pake akses customer gk
         return ($this->loadSetting('customerPrivileges') == 1) ? true : false; 
    }
          
    function useSalesPrivileges(){  
        // buat cek setiap USER pake akses sales gk
         return ($this->loadSetting('salesPrivileges') == 1) ? true : false; 
    }
    
    
    function useBusinessUnitPrivileges(){  
        // buat cek setiap USER pake akses sales gk 
         return ($this->loadSetting('businessUnitPrivileges') == 1) ? true : false; 
    }
    
    function useCreatedByPrivileges(){
        // cek kalo centangan hak akses dicentang, hanya bisa lihat data sendiri
        
        // 1, semua dibatasin, biasa utk aplikasi end user
        // 2, tergantung akses, kedepannya baru diupdate
        
        $createdByPrivileges = $this->loadSetting('createdByPrivileges');
        if($createdByPrivileges == 1){
            return true;
        }
        
        return false; // sementara
    }
    
    function usePaymentMethodPrivileges(){ 
     
        // buat cek setiap MODUL pake akses gudang gk
        $sql = 'select pkey from '.$this->tableSecurityObject.' where usepaymentmethodprivileges = 1';
        $rs = $this->oDbCon->doQuery($sql);
        
        return (!empty($rs)) ? true : false;
    }
    
    function hasWarehousePrivileges($securityObject = ''){
        
        if( empty($securityObject)) $securityObject = $this->securityObject;
        
        $sql = 'select usewarehouseprivileges from '.$this->tableSecurityObject.' where modulecode = '.$this->oDbCon->paramString($securityObject);
        $rs = $this->oDbCon->doQuery($sql);
        
        return (!empty($rs) && $rs[0]['usewarehouseprivileges'] == 1) ? true : false;
        
    }
    
    function getWarehouseCriteria($tableName = '', $warehouseKey = array()){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        // check if module has warehouse privilleges
        if (!$this->hasWarehousePrivileges()) return '';
        
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allwarehouseaccess'] == 1) ? true : false;
            
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
          
         $rsWarehouse = $employee->getWarehouseAccess($this->userkey); 
         if(!empty($warehouseKey))
           $rsWarehouse = array_merge($rsWarehouse, $warehouseKey); 

        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali

        if (!empty($rsWarehouse)){  
            $fieldName = ($tableName == $this->tableWarehouse) ? 'pkey' : 'warehousekey';
            $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsWarehouse ,',').')  )'; 
        }

        return $criteria;

         
    }
     
    function getCustomerCriteria($tableName = '', $customerKey = array()){
        
         
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        // kalo gk pake customer priveleges langsung return aj
        if(!$this->useCustomerPrivileges()) return '';
         
        // check if module has customer privilleges
        //if (!$this->hasCustomerPrivileges()) return '';
        
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allcustomeraccess'] == 1) ? true : false;
        
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
         
        
        $rsCustomer = $employee->getCustomerAccess($this->userkey);  
        if(!empty($customerKey))
           $rsCustomer = array_merge($rsCustomer, $customerKey);
             
        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali
        
        if (!empty($rsCustomer)){  
            $fieldName = ($tableName == $this->tableCustomer) ? 'pkey' : 'customerkey';
            $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsCustomer ,',').')  )'; 
        }
         
        return $criteria;
    }
     
    
    function getCOACriteria($tableName = '', $fieldName = ''){
        
        $chartOfAccount = new ChartOfAccount();
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName;
        
        if (!defined('USE_GL') || !USE_GL) return '';
        
        $employee = new Employee();
        $rsCOA = $employee->getCOAAccess($this->userkey); 
        $coakey = implode(',', $rsCOA);
          
        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses coa sama sekali
        
        if (!empty($rsCOA)){  
            
            if (empty($fieldName))
                $fieldName = ($this->tableName == $chartOfAccount->tableName) ? $tableName.'.pkey' :  $tableName.'.coakey';
            
            if (!is_array($fieldName)) 
                $fieldName = array($fieldName);
            
            $arrCriteria = array();
            foreach($fieldName as $row) 
                array_push($arrCriteria,  $row.' in ('.$coakey.')');
            
            $criteria = ' and ('. implode(' or ' , $arrCriteria) .')'; 
        }
         
        return $criteria;
    }
    
    function getSalesCriteria($tableName = '', $salesKey = array(), $columns = array()){
         
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        // kalo gk pake customer priveleges langsung return aj
        if(!$this->useSalesPrivileges()) return '';
        
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allsalesaccess'] == 1) ? true : false;
        
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
         
        $rsSales = $employee->getSalesAccess($this->userkey);  
        if(!empty($salesKey))
           $rsSales = array_merge($rsSales, $salesKey);
             
        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali
        
        if (!empty($rsSales)){  
            $fieldName = ($tableName == $this->tableEmployee) ? 'pkey' : 'saleskey';
            
            if(empty($columns)){ 
                $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsSales ,',').')  )'; 
            }else{
                $arrCriteria = array();
                foreach($columns as $col)
                     array_push($arrCriteria, $col.' in ('.$this->oDbCon->paramString($rsSales ,',').')' ); 
                
                $criteria = ' and ('.implode(' OR ', $arrCriteria).')';
            }
                
        }
        
        return $criteria;
    }
     
    function getFreightChannelCriteria($tableName = '', $transportationtypekey = array()){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allfreightchannelaccess'] == 1) ? true : false;
            
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
          
         $rsFreightChannel = $employee->getFreightChannelAccess($this->userkey); 
         if(!empty($transportationtypekey))
           $rsFreightChannel = array_merge($rsFreightChannel, $transportationtypekey); 

        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali

        if (!empty($rsFreightChannel)){  
            $fieldName = ($tableName == $this->tableTransportationType) ? 'pkey' : 'transportationtypekey';
            $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsFreightChannel ,',').')  )'; 
        }

        return $criteria;

         
    }
    
     function getPaymentMethodCriteria($tableName = '', $warehouseKey = array()){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        // kalo gk pake customer priveleges langsung return aj
        if(!$this->usePaymentMethodPrivileges()) return '';
         
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allpaymentmethodaccess'] == 1) ? true : false;
            
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
          
         $rsPaymentMethod = $employee->getPaymentMethodAccess($this->userkey); 
         if(!empty($warehouseKey))
           $rsPaymentMethod = array_merge($rsPaymentMethod, $warehouseKey); 

        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali

        if (!empty($rsPaymentMethod)){  
            $fieldName = ($tableName == $this->tablePaymentMethod) ? 'pkey' : 'paymentmethodkey';
            $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsPaymentMethod ,',').')  )'; 
        }
 
        return $criteria;
         
    }
    
    
    function getBusinessUnitCriteria($tableName = '', $businessUnitKey = array()){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return ''; 
        if(!$this->useBusinessUnitPrivileges()) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $employee = new Employee();
        $rsEmployee = $employee->getDataRowById($this->userkey);
        $hasAllAccess = (!empty($rsEmployee) && $rsEmployee[0]['allbusinessunitaccess'] == 1) ? true : false;
            
         // sementara kalo ad all access, return true, agar gk byk criteria
        if ($hasAllAccess) return '';
          
         $rsBusinessUnit = $employee->getBusinessUnitAccess($this->userkey); 
         if(!empty($businessUnitKey))
           $rsBusinessUnit = array_merge($rsBusinessUnit, $businessUnitKey); 

        $criteria = ' and false '; // default gk bisa lihat data kalo gk punya akses gudang sama sekali

        if (!empty($rsBusinessUnit)){  
            $fieldName = ($tableName == $this->tableBusinessUnit) ? 'pkey' : 'businessunitkey';
            $criteria = ' and ( '.$tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($rsBusinessUnit ,',').')  )'; 
        }

        return $criteria;

    }
    
    
    function getCreatedByCriteria($tableName = ''){
        
        // kalo dr sistem, gk boleh batasin...
        if(empty($this->userkey)) return ''; 
        if(!$this->useCreatedByPrivileges()) return '';
        
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $criteria = ' and ( '.$tableName.'.createdby =  '.$this->oDbCon->paramString($this->userkey).' )'; 
        return $criteria; 
        
    }
    
    
    function formatDataFromAPI($value, $type){
		// khusus dari database... karena kalo tgl bisa bedaa format
		
		// dikirimnya ad 2 standart, bisa d / m / Y dari payload
		// bisa jg dr db Y-m-d
		
		//$this->setLog($value . ' >> ' .$type,true);
        switch($type){
            case 'date' : $value = $this->formatDBDate($value); 
                         break;
            case 'datetime' : $value = $this->formatDBDate($value, 'd / m / Y H:i'); 
                              break; 
        } 


        return $value;
    }
    
    function formatDataForUpdate($value, $type ){
       
        switch($type){
            case 'date' : 
            case 'datetime' : $value = $this->oDbCon->paramDate($value,' / '); 
                              break;
            case 'integer' :   
            case 'number' : $value = $this->oDbCon->paramString($this->unFormatNumber($value)); 
                            break;
            case 'raw' : $value = '\''.addslashes($value).'\''; 
                         break; 
            case 'noformat' :  // utk yg lang, dihandle manual
                         break; 
            case 'image' :   
            case 'file' :   
                            if($this->useStorage('S3'))  
                               $value = (isset($value['name'])) ? $value['name'] : '';
                              
                            $value = $this->oDbCon->paramString($value); // nanti perlu diupdate kalo ad rename file
                
							break;
            default :   $value = $this->oDbCon->paramString($value);
                         break; 
        } 


        return $value;
    }
    
    function prepareDataDetail($arrParam){
          
        foreach ($this->arrData as $key=>$data){
           // $data = $this->arrData[$fieldNames[$i]]; 
            $dataType = (isset($data[1]) && !is_array($data[1])) ? $data[1] : ''; // nanti perlu diupdate kalo datatype ada dalam di array               
            $options = (isset($data[1]) && is_array($data[1])) ? $data[1] : array(); 
             
            // if has detail  
            if (isset($options['dataDetail'])){  
                
                $detailOptions = $options['dataDetail'];
                
                // if only have 1 dimension array
                if (!isset($detailOptions[0])){ 
                    $temp = $detailOptions;
                    $detailOptions = array();
                    array_push($detailOptions, $temp);  
                }    
                
                foreach($detailOptions as $detailOption) { 
                    $this->updateDataDetail($arrParam, $data[0],$detailOption);   
                }
                
            }
 
        }
        
    }
    
    function prepareData($arrParam, $prepareFor = INSERT_DATA){
        
        $employee = new Employee();
        $arrData = array();
         
        $tableName = $this->tableName; 
        $rsKey = $this->getTableKeyAndObj($tableName,array('key'));
        $tablekey = $rsKey['key']; 
           
		
        foreach ($this->arrData as $key=>$data){ 
            // $dataType = (isset($data[1]) && !is_array($data[1])) ? $data[1] : ''; // nanti perlu diupdate kalo datatype ada dalam di array           
            
            $optiontemp = (isset($data[1])) ? $data[1] : '';  
	 
			if (is_array($optiontemp)) 
				$dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
			else
				$dataType = $optiontemp;
			
			//kalo tipenya file / image, 
			if(in_array($dataType, array('image','file'))){ 
				
                if($this->useStorage('S3')){
//                    $arrParam[$data[0].'-mnv-file']['isHeader'] = true ; // lupa untuk ap
                    
                    // hrus ditampung, kalo gk pengaruh ke arrParam nya
//                    $arrUploadParam = $arrParam[$data[0]];
//                    $arrUploadParam[$data[0]]['uploadFolder'] = $optiontemp['uploadFolder'] ;
//                    array_push($this->uploadQueue, $arrUploadParam[$data[0]]); 
                    
                    $arrParam[$data[0]]['uploadFolder'] = $optiontemp['uploadFolder'] ;
                    array_push($this->uploadQueue, $arrParam[$data[0]]); 
                }else{
                     $arrUpload = array();

                    // awalnya $optiontemp['opt']
                    $arrUpload['uploadFolder'] = (isset($optiontemp['uploadFolder'])) ? $optiontemp['uploadFolder'] : $this->uploadFolder; 
                    $arrUpload['token'] = $arrParam[$optiontemp['token']];
                    $arrUpload['fileName'] = $arrParam[$optiontemp['fileName']]; 

                    array_push($this->uploadQueue, $arrUpload); 
                 }
				
			}
             
            $valueToFormat = (isset($arrParam[$data[0]])) ? $arrParam[$data[0]] : ''; 
            $value =  $this->formatDataForUpdate($valueToFormat, $dataType);
            
            array_push($arrData, array( 
                            'field' => $key,
                            'value' => $value, 
                            'format' => $dataType,
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
            
            
            //array_push($arrData, array($key, $value));
        }
        
        
        if ($prepareFor == INSERT_DATA){  
            //array_push($arrData, array('createdon', 'now()'));
            //array_push($arrData, array('createdby', $this->oDbCon->paramString($this->userkey)));   
            
                        
            array_push($arrData, array( 
                            'field' => 'createdon',
                            'value' => 'now()', 
                            'format' => '',
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
            
                        
            array_push($arrData, array( 
                            'field' => 'createdby',
                            'value' => $this->oDbCon->paramString($this->userkey), 
                            'format' => '',
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
            
            
            if ($this->isMultiCompany() && !isset($this->arrData['companykey'])){  
                $companykey = 1;
                $rsCompany =  $employee->getAccessCompany($this->userkey); 
                $companykey = $rsCompany[0]['companykey'];
                if (!empty($arrParam['selMainCompany'])){ 
                    if(in_array($arrParam['selMainCompany'], $rsCompany)) 
                        $companykey = $arrParam['selMainCompany']; 
                }  
 
                //array_push($arrData, array('companykey', $this->oDbCon->paramString($companykey)));  
                
                    array_push($arrData, array( 
                            'field' => 'companykey',
                            'value' => $this->oDbCon->paramString($this->$companykey), 
                            'format' => '',
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
                
            }  

        }else{ 
           // array_push($arrData, array('modifiedon', 'now()'));
          //  array_push($arrData, array('modifiedby', $this->oDbCon->paramString($this->userkey)));   
            
             array_push($arrData, array( 
                            'field' => 'modifiedon',
                            'value' => 'now()', 
                            'format' => '',
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
            
                        
            array_push($arrData, array( 
                            'field' => 'modifiedby',
                            'value' => $this->oDbCon->paramString($this->userkey), 
                            'format' => '',
                            'tablekey' => $tablekey,
                            'tablename' => $tableName
                        )); 
            
        } 
         
        return $arrData;
    }
    
    
   /* function prepareData($arrParam, $prepareFor = INSERT_DATA){
        
        $employee = new Employee();
        $arrData = array();
  
        foreach ($this->arrData as $key=>$data){
           // $data = $this->arrData[$fieldNames[$i]]; 
            $dataType = (isset($data[1]) && !is_array($data[1])) ? $data[1] : ''; // nanti perlu diupdate kalo datatype ada dalam di array               
            
            $options = (isset($data[1]) && is_array($data[1])) ? $data[1] : array(); 
            
            
            // if has detail
            if (isset($options['dataDetail'])){ 
                 
                $detailOptions = $options['dataDetail'];
                
                // if only have 1 dimension array
                if (!isset($detailOptions[0])){ 
                    $temp = $detailOptions;
                    $detailOptions = array();
                    array_push($detailOptions, $temp);  
                }    
                
                foreach($detailOptions as $detailOption) 
                  $this->updateDataDetail($arrParam, $data[0],$detailOption);   
                
            }

            $value = $this->formatDataForUpdate($arrParam[$data[0]], $dataType);
            
           array_push($arrData, array($key, $value));
        }
        
        
        if ($prepareFor == INSERT_DATA){  
            array_push($arrData, array('createdon', 'now()'));
            array_push($arrData, array('createdby', $this->oDbCon->paramString($this->userkey)));   
            
            if ($this->isMultiCompany() && !isset($this->arrData['companykey'])){  
                $companykey = 1;
                $rsCompany =  $employee->getAccessCompany($this->userkey); 
                $companykey = $rsCompany[0]['companykey'];
                if (!empty($arrParam['selMainCompany'])){ 
                    if(in_array($arrParam['selMainCompany'], $rsCompany)) 
                        $companykey = $arrParam['selMainCompany']; 
                }  
 
                array_push($arrData, array('companykey', $this->oDbCon->paramString($companykey)));  
            }  

        }else{ 
            array_push($arrData, array('modifiedon', 'now()'));
            array_push($arrData, array('modifiedby', $this->oDbCon->paramString($this->userkey)));   
        } 
         
        
        return $arrData;
    }*/
    
    function deleteSubDetail($arrData, $arrParam, $deletedKey){
         
        // harusnya selalu ad tablename, kalo gk ad, error. bahaya kalo pake tableNameDetail
        //$tableName = (isset($arrData['tableName']) && !empty($arrData['tableName'])) ? $arrData['tableName'] : $this->tableNameDetail ;
         
        if(!isset($arrData['tableName'])) throw new Exception($this->errorMsg[212]);
        
        $dataset = $arrData['dataset'];   
        $tableName = $arrData['tableName'];
        
        $sql = 'delete from '.$tableName.' where refkey in ('.$deletedKey.') ' ;  
        $this->oDbCon->execute($sql);

        // utk level 3 keatas
        /*foreach($dataset as $key=>$options){
             if (isset($options[1]['dataDetail']['dataset'])){     
                $detailOptions = $options[1]['dataDetail'];   
                //nanti dulu, ini utk 3 level dst 
                foreach($detailOptions as $detailOption){   
                    $this->deleteSubDetail($detailOption,); 
                } 
             }  
        } */
        
       
    }
    
    function updateDataDetail($arrParam, $headerKeyField, $arrData, $level = 0, $headerRowIndex = 0){
    
        $arrParamTemp = $arrParam; 
        
        // hidDetailKey gk boleh sama
        // jika level 2 dst, harus menggunakan nama lain  
        $dataset = $arrData['dataset'] ?? array();
         
        $hasMoreDetail = false; 
        // cek kalo 2 level atau lebih.. 
        foreach($dataset as $key=>$options){
            // cek utk satu detail dan lebih dr satu detail
             if (isset($options[1]['dataDetail']['dataset']) || isset($options[1]['dataDetail'][0]['dataset'])){      
                    $hasMoreDetail = true; 
                    break;  
             }  
        } 
        
        
        if($level > 0){       
            // kalo ad multilevel, shift semua index   
              foreach($dataset as $key=>$field){     
                   
                  //$this->setLog('$arrParamTemp['.$field[0].'] = array()' );
                  //$arrParamTemp[$field[0]] =  $arrParam[$field[0]][$headerRowIndex];
                  $arrParamTemp[$field[0]] = (is_array($arrParam[$field[0]])) ? $arrParam[$field[0]][$headerRowIndex] : $arrParam[$field[0]];  
                  // sementara untuk model refheaderkey, yg cuma ad 1 dimensi, masihkah ??
       
              } 
               
                //$this->setLog("shifted");
                //$this->setLog($arrParamTemp);
        
        }
        
 
        // biasanya $headerKeyField = 'pkey'
        // biasanya $arrParamTemp['pkey'], dan nilai pkey sudah diassign
        // $this->setLog('$arrParamTemp['.$headerKeyField.']');
        $refHeaderKey = $arrParamTemp[$headerKeyField]; 
        
        $fieldNames = array_keys($dataset);   
        $tableName = (isset($arrData['tableName']) && !empty($arrData['tableName'])) ? $arrData['tableName'] : $this->tableNameDetail ;
        $reffield = (isset($arrData['refField']) && !empty($arrData['refField'])) ? $arrData['refField'] : 'refkey' ; 
 
        // patokan key detail, sementara selalu 'pkey';
        $detailkey = $dataset['pkey'][0]; 
        $arrKeys = array_keys($dataset);
         
        // utk form yg blm ditambahkan hidDetailKey
        if (!isset($arrParamTemp[$detailkey]))
            $arrParamTemp[$detailkey] = array();
        
        // set parameter     
        //$this->setLog($detailkey);
        $totalRows = count($arrParamTemp[$detailkey]); // hiDetailKeys harus diisi, utk patokan total rows.
        $totalField = count($arrKeys);
    
        
        // existing detail keys
        $sql = 'select pkey from '.$tableName.' where '.$reffield.' = ' . $this->oDbCon->paramString($refHeaderKey);  
        //$this->setLog($sql);
        $rsDetail = $this->oDbCon->doQuery($sql); 
        $arrDetailKeys = array_column($rsDetail,'pkey');
        
        // posted detail keys 
        $arrPostedKeys = $arrParamTemp[$detailkey];
        
        // delete data yg sudah diremove di UI / atau yg nilainya kosong  
        $deletedKey = array_diff($arrDetailKeys, $arrPostedKeys);  
        if (!empty($deletedKey)){ 
            $deletedKey = implode(',',$deletedKey); 
            $sql = 'delete from '.$tableName.' where pkey in ('.$deletedKey.') ' ; 
            //$this->setLog($sql,true);
            
            $this->oDbCon->execute($sql);
            
            // kalo ad detail table, harus dihapus jg
            // karena loop nya gk nyampe, kalo 1 rows dihapus... sudah kehapus duluan di rekursif sebelumnya
            if($hasMoreDetail){
                
                 foreach($dataset as $key=>$options){ 
                    if (isset($options[1]['dataDetail']['dataset'])){    
                        $detailOptions = $options[1]['dataDetail'];    
                        $this->deleteSubDetail($detailOptions,$arrParamTemp, $deletedKey); 
                     }  
                } 
                
            }
        }
         
        // get table key  
        $sql = 'select pkey from tablekey where tablename = '.  $this->oDbCon->paramString($tableName);
        $rsTableKey = $this->oDbCon->doQuery($sql);  
        $tablekey = $rsTableKey[0]['pkey'];
        
        // insert / update        
        for($i=0;$i<$totalRows;$i++){  

            $arrCol = array();  
            
            $hasEmptyValue = false;
            
            $prepareFor = (in_array($arrPostedKeys[$i], $arrDetailKeys)) ? UPDATE_DATA : INSERT_DATA ; 

            for($j=0;$j<$totalField;$j++){  
                    /*if ($arrKeys[$j] == 'pkey')
                        continue;*/

                    $elName = $dataset[$arrKeys[$j]][0];
                
                    // kalo detail key tdk terdaftar, reset detailkeynya 
                    if ($arrKeys[$j] == 'pkey' && !in_array($arrParamTemp[$elName][$i],$arrDetailKeys ))
                        $arrParamTemp[$elName][$i] = '';
                      
                    //$this->setLog($arrKeys[$j] .' == pkey  && empty($arrParamTemp['.$elName.'][$i])');    
                    if($arrKeys[$j] == 'pkey' && empty($arrParamTemp[$elName][$i])){    
                        $detailkey = $this->getNextKey($tableName); 
                        $arrParam[$elName][$i] = $detailkey; // khusus ini pake arrParam, agar keupdate 
                        $arrParamTemp[$elName][$i] = $arrParam[$elName][$i];
                       // $this->setLog('new pkey for $arrParamTemp['.$elName.']['.$i.'] => '. $detailkey);
                    }
                
                    $dbField = $arrKeys[$j];  
                
                    // kalo ad parameter kedua, cek array atau bkn
                    $dataType = '';
                    //$this->setLog($arrKeys[$j]);
                    $optiontemp = (isset($dataset[$arrKeys[$j]][1])) ? $dataset[$arrKeys[$j]][1] : '';  
                  
                    if (is_array($optiontemp)) 
                        $dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
                    else
                        $dataType = $optiontemp;
                        
                    $value = ($dataType == 'ref' ) ? $arrParamTemp[$elName] : $arrParamTemp[$elName][$i];   
 
				//  $this->setLog('$value = ('.$dataType .' == \'ref\' ) ? $arrParamTemp['. $elName .' ] : $arrParamTemp['.$elName.'][$i]',true);   
					//$this->setLog('$arrParamTemp = ' .  $arrParamTemp[ $elName ] );   
                     
                    if (isset($optiontemp['mandatory']) && $optiontemp['mandatory'] && empty($value) ){ 
                        $hasEmptyValue = true;
                        break;
                    } 
                 
                    //kalo tipe s3/upload file, yg dicek value name, JANGAN tmp_name 
                     if($this->useStorage('S3') && in_array($dataType, array('image','file')) && empty($value['name'])){
                        $hasEmptyValue = true;
                        break;
                     }
                
                        //kalo tipenya file / image,
                
                        if(in_array($dataType, array('image','file'))){
                            
                            if($this->useStorage('S3')){ 
                                $arrParam[$elName][$i]['uploadFolder'] = $optiontemp['uploadFolder'] ; 
                                array_push($this->uploadQueue, $arrParam[$elName][$i]); 
                                
                            }else{ 
                                    $arrUpload = array();
                                    // awalnya $optiontemp['opt']
                                    $arrUpload['uploadFolder'] = (isset($optiontemp['uploadFolder'])) ? $optiontemp['uploadFolder'] : $this->uploadFolder; 
                                    $arrUpload['token'] = $arrParam[$optiontemp['token']];
                                    $arrUpload['fileName'] = $arrParam[$optiontemp['fileName']][$i]; 
                                    array_push($this->uploadQueue, $arrUpload); 
                             }
                            
                        }
                 
                    // format array penampung
                    array_push($arrCol, array( 
                                                'field' => $dbField,
                                                'value' => $this->formatDataForUpdate($value, $dataType),
                                                //'rawvalue' => $value,
                                                'format' => $dataType,
                                                'tablekey' => $tablekey,
                                                'tablename' => $tableName
                                            )); 

                    /*if ($prepareFor == UPDATE_DATA){ 
                        array_push($updateQuery,$dbField . ' = ' .  $value); 
                    }*/
            }
            
            $sql = '';
            if ($prepareFor == INSERT_DATA && !$hasEmptyValue) { 
                $sql = 'insert into '.$tableName.' ('.implode(',',array_column($arrCol, 'field')).') values ('.implode(',',array_column($arrCol, 'value')).')';   
            }else if ($prepareFor == UPDATE_DATA ){ 
               if (!$hasEmptyValue){ 
                   
                    // get previous data
                    $rsPrev = $this->getPrevData($tableName, $arrPostedKeys[$i]);
                   
                    // format update query
                    $updateQuery = array();
                    foreach($arrCol as $row){  
                        // kalo ad requestid dr API, gk boleh diupdate lg
                        if($row['field'] == 'requestid') continue; 
                        array_push($updateQuery,$row['field'] . ' = ' .  $row['value']); 
                    }
                   
                    $sql = 'update '.$tableName.' set '. implode(',',$updateQuery).' where pkey = ' . $this->oDbCon->paramString($arrPostedKeys[$i]);
                   
                    // log changes
                    $this->updateTransactionChangedLog($rsPrev,$arrCol);
                   
               }else{ 
                    $sql = 'delete from '.$tableName.' where pkey = ' . $this->oDbCon->paramString($arrPostedKeys[$i]);
                     
               }
            } 
                   
            //$this->setLog($sql,true);
            
            if (!empty($sql))
                $this->oDbCon->execute($sql);
            
            if($hasMoreDetail){  
                              
                // harus ad index row nya... 
                foreach($dataset as $key=>$options){
                    
                    // harus bisa memfasilitasi kalo ad beberapa detail set 
                    //$this->setLog($options); 
                    
                    if (!isset($options[1]['dataDetail'])) continue;  
                        
                    $detailOptions = $options[1]['dataDetail'];  

                    // if only have 1 dimension array
                    if (!isset($detailOptions[0])){ 
                        $temp = $detailOptions;
                        $detailOptions = array();
                        array_push($detailOptions, $temp);  
                    }    

                    foreach($detailOptions as $detailOption){     
                        $this->updateDataDetail($arrParam, $options[0], $detailOption, $level + 1 ,$i); // detailOptions harusnya $this->arrItem
                    } 

                } 
 
            }
        }
         
    }
    
    function updateData($arrParam,$updateFor =  INSERT_DATA, $transactionLog = true){  
         
        $tableName = $this->tableName; 
        
        if ($updateFor == UPDATE_DATA){ 
            // get previous data
            $rsPrev = $this->getPrevData($tableName, $arrParam['pkey']); 
        }
        
        $arrCol = $this->prepareData($arrParam,$updateFor);
        
        if($updateFor == INSERT_DATA)
            $sql = 'insert into '.$this->tableName.'('.implode(',',array_column($arrCol, 'field')).') values ('.implode(',',array_column($arrCol, 'value')).')';   
        else{  
            
            // format update query
            $updateQuery = array();
            foreach($arrCol as $row)
                 array_push($updateQuery,$row['field'] . ' = ' .  $row['value']); 


            $sql = 'update '.$this->tableName.' set '. implode(',',$updateQuery).' where pkey = ' . $this->oDbCon->paramString($arrParam['pkey']);
        }
        
         $this->oDbCon->execute($sql);	
        
        
        // update detail
        $this->prepareDataDetail($arrParam);
        
        // sementara khusus kalo edit saja
        if ($updateFor == UPDATE_DATA){ 
            $this->updateTransactionChangedLog($rsPrev,$arrCol);
        }
        
        if ($transactionLog)
            $this->setTransactionLog($updateFor,$arrParam['pkey']);
         
    }
     
    function manipulateDataBeforeUpdateData($arrParam){
        // kepake buat manipulasi data, misalnya utk getNewCode karena diproses sebelum normalize
        // bedain add / edit pake hidId
        
        return $arrParam;
    }
    
    function addData ($arrParam){
        
        $arrParam = $this->manipulateDataBeforeUpdateData($arrParam);
             
		$arrayToJs = array();
		
        if ($this->isTransaction)
            $arrParam['selStatus'] = 1;
            
		try{
	
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            $customCode = (isset($arrParam['selCustomCode']) && !empty($arrParam['selCustomCode'])) ? $arrParam['selCustomCode'] : '';
            
			$pkey = $this->getNextKey($this->tableName); 
            $arrParam['pkey'] = $pkey;
            
            $isTest = (isset($this->testnewCode)) ? true : false;
                
            $code = ($isTest) ? rand().time() : $this->getNewCustomCode($arrParam,$customCode); 
            if(!is_array($code)){
                $arrParam['code'] = $code; 
            }else{ 
                $arrParam['code'] = $code[0]; 
                $arrParam['codectr'] = $code[1];
            }
              
            $arrParam = $this->normalizeParameter($arrParam);
                
	 		$arrayToJs = $this->validateForm($arrParam);
			if (!empty($arrayToJs)){ 
			    // harusnya throw error agar transactionCounter nya jd 0 lg
                // karena kalo gk, bermasalah ketika proses add data dalam for
                //$errMsg = array_column($arrayToJs,'message');
                //throw new Exception(implode(chr(13),array_column($arrResponse,'message'))); 
                //die;
                
                // JGN JD THROW, karena bebrapa ngecek berhasil atau gk dari return nya.
                // utk FOR, define transaction baru saja
                return $arrayToJs;
            }
               
            $this->updateData($arrParam,INSERT_DATA);
               
		    $arrayToJs = $this->afterUpdateData($arrParam,INSERT_DATA); 
            if (!empty($arrayToJs))  return $arrayToJs; // test dulu buat API Logol

            $arrayToJs = $this->saveAndProceed($arrParam); 
            if (!empty($arrayToJs) && !$arrayToJs[0]['valid'])
                return $arrayToJs;
            
            //update code belakangan, mengurangi resiko code kelompat
            if($isTest){
              $code = $this->getNewCustomCode($arrParam,$customCode);   
                
                if(!is_array($code)){ 
                    $sqlUpdate  = ' code = '.$this->oDbCon->paramString($code);
                }else{   
                    $sqlUpdate  = ' code = '.$this->oDbCon->paramString($code[0]);
                    
                    if(isset($this->arrData['codectr'])) $sqlUpdate  .= ', codectr = '. $this->oDbCon->paramString($code[1]); 
                }

              $sql = 'update '.$this->tableName.' set '.$sqlUpdate.' where pkey = ' .$this->oDbCon->paramString($arrParam['pkey']);
              $this->oDbCon->execute($sql);
            } 
            
             
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
             
            if(isset($this->privilegesCriteria)) $this->privilegesCriteria = false;
            $rs = $this->searchData($this->tableName.'.pkey',$arrParam['pkey'],true);   
            
            // utk jaga2 saja kalo masi kepake obj nya
            $this->privilegesCriteria = true;
            
            $arrayToJs[0]['data'] = $rs[0];
             
            //$arrayToJs[0]['newpkey'] = $arrParam['pkey'];
             
		    $this->afterCommitUpdateData($rs[0]['pkey'],$rs); 
            
		}catch(Exception $e){ 
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	
		  
        
		return $arrayToJs;
			
	}
 
    function refillDetail($headerPkey,$detailRow, &$arrParam,$level=0){
        //$this->setLog($level . ' ====== ',true);
        
        // Dengan asumsi api selalu kirim pkey detail
        $pkeyParamName = $detailRow['dataset']['pkey'][0];

        // loop detail yg dikirim dr API 
        $DELETE_KEYWORD = '_delete'; 
        $willBeDeletedParamName = $DELETE_KEYWORD.'-'.$pkeyParamName;
        
        $currTableName = (isset( $detailRow['tableName'] )) ? $detailRow['tableName'] : $this->tableNameDetail; 
        $usedDbFields = array_keys($detailRow['dataset']); 
         
        // Ambil row data detilnya berdasarkan pkey header
        // Dengan asumsi semua detil punya field db 'refkey'            
        // yg _delete gk perlu di query,
        
        $excludeForDeletedCriteria = (isset($arrParam[$willBeDeletedParamName]) && !empty($arrParam[$willBeDeletedParamName])) ? ' and  '.$currTableName.'.pkey not in ('.$this->oDbCon->paramString($arrParam[$willBeDeletedParamName],',').') ' : '';
            
        $sql = 'SELECT '.implode(',', $usedDbFields).' FROM '.$currTableName.' WHERE '.$currTableName.'.refkey = '.$this->oDbCon->paramString($headerPkey).$excludeForDeletedCriteria;
        $rs = $this->oDbCon->doQuery($sql);
 
        // ganti index tiap rownya dengan pkeynya
        $detailRsCols = array_column($rs, null, 'pkey'); 
		$currDetailRsColPkeys = array_keys($detailRsCols);

		// ubah dulu yg format tgl dr database
        foreach($detailRow['dataset'] as $dbfield=>$dataset){  
			$optiontemp = (isset($dataset[1])) ? $dataset[1] : '';   
			if (is_array($optiontemp)) 
				$dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
			else
				$dataType = $optiontemp;
			
            foreach($detailRsCols as $key=>$dbRow){    
				$detailRsCols[$key][$dbfield] =  $this->formatDataFromAPI($dbRow[$dbfield], $dataType); 
            } 
		}
		
		
		if(isset($arrParam[$pkeyParamName])){ 
			for ($j=0; $j<count($arrParam[$pkeyParamName]); $j++){ 
				
				// nilai detail pkey yg dikirim
				$currPkey = $arrParam[$pkeyParamName][$j];
				
				// cek yg dr database
				foreach($detailRsCols as $key=>$row){  
				  // pkey yg dikirim udah ada di DB; update yang lama 
				   if (in_array($currPkey, $currDetailRsColPkeys)){
						  foreach ($detailRow['dataset'] as $dbField => $dataset){
							$currParamName = $dataset[0]; 
							if (!array_key_exists($currParamName, $arrParam))  continue;   
							$detailRsCols[$currPkey][$dbField] = $arrParam[$currParamName][$j];
						  } 
					}
				}

				// add yg baru, pkey = 0  
				if(empty($currPkey)){
					$newRow = array(); 
					foreach ($detailRow['dataset'] as $dbField => $val){ 
						$currParamName = $val[0]; 
						if (!array_key_exists($currParamName, $arrParam)) continue;
						$newRow[$dbField] = $arrParam[$currParamName][$j];
					} 

					array_push($detailRsCols,$newRow ); // hati2 dengan index
				}  

			}
		}
		
        $firstIndexField = array();
        foreach($detailRow['dataset'] as $dbfield=>$dataset){ 
 
			$optiontemp = (isset($dataset[1])) ? $dataset[1] : '';   
			if (is_array($optiontemp)) 
				$dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
			else
				$dataType = $optiontemp;


            // $paramName = selWarehousekey
            // $dbfield = warehousekey
            
            $paramName = $dataset[0];
            
            if(empty($firstIndexField)) {
                $firstIndexField['paramName'] = $paramName;
                $firstIndexField['detail'] = (isset($dataset[1]['dataDetail'])) ? $dataset[1]['dataDetail'] : array();
            }
            
            if($paramName == 'pkey') continue; 
            
            // kalo ref lewatin
            if(isset($dataset[1]) && $dataset[1] == 'ref') continue;
            
            // harus direset karena dr awal sudah ad nilainya, dioverwrite dengan hasil dr query, tp...
            // kalo level 1 gk boleh direset terus
            // hanya boleh direset sekali diawal  
             if(!in_array( $paramName,$arrParam['_mnv-reset'])){ 
                $arrParam[$paramName] = array();     
                array_push($arrParam['_mnv-reset'],$paramName);
            }
           
			
            foreach($detailRsCols as $dbRow){    
				// gk perlu format ulang, karena sudha diupdate diatas pas narik dr database
                array_push($arrParam[$paramName],$dbRow[$dbfield]);  
            }
            
        }
		
        $index = ($level == 0) ? 'hidTotalRows' : $firstIndexField['paramName'].'TotalRows'; 
        
        if(!isset($arrParam[$index][$level])) $arrParam[$index][$level] = array();
        array_push($arrParam[$index][$level], count($detailRsCols)); 
        //array_push($arrParam[$index][$level], count( $arrParam[$firstIndexField['paramName']] ));


        // utk level kedua sampe saat ini 1 detail hanya punya 1 subdetail, jd gk mungkin array dataset
        if(!empty($firstIndexField['detail'])){  
            $level += 1;
            // loop utk setiap baris 
            foreach($detailRsCols as $row)
                $this->refillDetail($row['pkey'],$firstIndexField['detail'],$arrParam,$level);  
        } 
		
    }
    
    
    function refillData($arrParam){ 
        
        // sementara karena arrParam perlu tau mana yg sudah direset, utk level kedua cuma boleh reset seklai
        $arrParam['_mnv-reset'] = array();
        
        // Ngisi data" header
        // Ambil pkey header dulu
        $headerPkey = -1;
        $rs = array();
        if (array_key_exists('pkey', $arrParam)){
            if (!empty($arrParam['pkey'])) $headerPkey = $arrParam['pkey'];
        }
        
        // harusnya sudah pasti ad pkey, karena sudah di fill di _process 
        // kalo API update berdasarkan code
        // nanti bisa berdasarkan request id jg misalnya
        if ($headerPkey == -1){
            if (array_key_exists('code', $arrParam)){
                if (!empty($arrParam['code'])){ 
                    $rs = $this->searchDataRow(array($this->tableName.'.pkey'),
                                                ' and '.$this->tableName.'.code = '.$this->oDbCon->paramString($arrParam['code']));
                    $headerPkey = (!empty($rs)) ? $rs[0]['pkey'] : $headerPkey;
                }
            }
        }
        
        // pkey header ga ketemu
        if ($headerPkey == -1) return $arrParam;
 
        // header harusnya gk perlu refill, karen sudah ad fungsi trim,
        // perlu isi ulang karena ad bebera pa yg mandatory
        
        $usedHeaderDbFields = array_keys($this->arrData);  
        $sql = "SELECT ".implode(',',$usedHeaderDbFields)." FROM ".$this->tableName." WHERE pkey = ".$this->oDbCon->paramString($headerPkey);
        $rowData =  $this->oDbCon->doQuery($sql);
        
        if (count($rowData) >= 1) {
          foreach($this->arrData as $dbfield=>$data) { 
              $paramName = $data[0];
              
              $dataType = (isset($data[1]) && !is_array($data[1])) ? $data[1] : ''; // nanti perlu diupdate kalo datatype ada dalam di array    
               
              // klao gk dikirim dr API, refill datanya,  karena ad beberapa yg mandatory
              if(!isset($arrParam[$paramName]))
                $arrParam[$paramName] =  $this->formatDataFromAPI($rowData[0][$dbfield], $dataType); 
              
              // kalo ada update dr API
             //$detailRsCols[$currPkey][$dbField] = $arrParam[$data[0]];
              
              
          }
        } 
  
        
        // -------------------------
        // Mastiin class punya detail
        // sementara khusus jenis transksi saja dulu
        
        $classHasDetail = false;
        $pkeyArrData  = $this->arrData['pkey'];
        $classHasDetail = (isset($pkeyArrData[1]['dataDetail']) && !empty($pkeyArrData[1]['dataDetail']) && is_array($pkeyArrData[1]['dataDetail'])) ? true : false; 

        
        if (!$classHasDetail) return $arrParam;

        // refill details
        $detailArrays = $pkeyArrData[1]['dataDetail']; 
        
        // reset ulang total rows, karena ad kemungkinan punya baris data lain
        $arrParam['hidTotalRows'] = array();
        
        foreach($detailArrays as $details) { 
			$this->refillDetail($arrParam['pkey'],$details,$arrParam);  
		}
		
        unset($arrParam['_mnv-reset']);
    
		//$this->setLog($arrParam,true);
        //die; 
        
        return $arrParam;
    }

    function manipulateParamFromApi($arrParam){
        return $arrParam;
    }

    function editData($arrParam){    
        
        $arrParam = $this->manipulateDataBeforeUpdateData($arrParam);
        
		$arrayToJs =  array();
	
        // ---- START > Bagian khusus API ----
        if(isset($arrParam['_mnv-api']) && $arrParam['_mnv-api'] == 1){
            // sementara kalo master gk perlu dulu, karena ad bugs gk keupdate  
            $arrParam = $this->refillData($arrParam);  
            $arrParam = $this->manipulateParamFromApi($arrParam); 
        } 
        // ---- END > Bagian khusus API ----
        
        if ($this->isTransaction) 
		  unset( $this->arrData['statuskey']); 
        
		try{		
	  	 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            $customCode = (isset($arrParam['selCustomCode']) && !empty($arrParam['selCustomCode'])) ? $arrParam['selCustomCode'] : '';
            
            $code = $this->getNewCustomCode($arrParam,$customCode);
                 
            $arrParam['pkey'] = $arrParam['hidId']; 
            
            if(!is_array($code)){
                $arrParam['code'] = $code; 
            }else{
                $arrParam['code'] = $code[0]; 
                $arrParam['codectr'] = $code[1];
            }
             
            $arrParam = $this->normalizeParameter($arrParam);
            
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs))  return $arrayToJs;
					
            // get prev data
            //$prevRS = $this->getCurrentData($arrParam['pkey']);
            
            $this->updateData($arrParam, UPDATE_DATA); 

		    $arrayToJs =  $this->afterUpdateData($arrParam,UPDATE_DATA);   
            if (!empty($arrayToJs))  return $arrayToJs; // test dulu buat API Logol
             
		    $arrayToJs = $this->saveAndProceed($arrParam); 
            if (!empty($arrayToJs) && !$arrayToJs[0]['valid'])
                return $arrayToJs;
            
            //$this->setLog($arrayToJs,true);
            
			//$this->oDbCon->rollback();
            $this->oDbCon->endTrans(); 
			
            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
            $rs = $this->searchData($this->tableName.'.pkey',$arrParam['pkey'],true);
            $arrayToJs[0]['data'] = $rs[0];
            
		    $this->afterCommitUpdateData($rs[0]['pkey'],$rs); 
            
			
		}catch(Exception $e){ 
			$this->oDbCon->rollback();  
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}			
		
		return $arrayToJs; 
	}
    
    function afterUpdateData($arrParam,$action){
        
		// upload file 
//		foreach($this->uploadQueue as $key=>$queue){ 
//			$uploadFolder = (isset($queue['uploadFolder'])) ? $queue['uploadFolder'] : $this->uploadFolder; 
//			$this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader'],'',$uploadFolder);  
//			
//			unset($this->uploadQueue[$key]);
//		}
		
    }
        
    function afterCommitUpdateData($pkey,$rs){  
		$this->uploadQueuingFile($pkey);
        
        // copy dari folder temp kalo S3
        //if ($this->useStorage && $fromAPI){
        //    // add sama edit beda
        //    // kalo edit, bisa aj gk ad ditemp
        //
        //    $this->duplicateS3Folder(array('sourcePath' => '', 'prefixPath' = > ''),$destinationPath);
        //}
    }
	
  function  removeDeletedFilesS3 ($pkey,  $arrData = '', $client = ''){
    // $arrData -> files to keep  
      
    // harus ada nama domain  
    if (empty(DOMAIN_NAME)) return;
      
    // Configuration
    $bucket = STORAGE['bucket'];
      
    // kelompokin dulu per folder
    $arrFilesInFolder = $this->reindexDetailCollections($arrData,'folder');
      
    foreach($arrFilesInFolder as $folderName=>$folderRow){
            
            $folder = DOMAIN_NAME.'/'.$folderName; // must end with '/'
            if(substr($folder,-1) <> "/") $folder  .= '/'; 
            $folder .= $pkey.'/';  

            $keepList =  array_column($folderRow,'fileName');

            $client = (!empty($client)) ? $client : $this->initS3Client();

            try {
                // List all objects in the specified folder
                $objectsToDelete = [];
                $params = [
                    'Bucket' => $bucket,
                    'Prefix' => $folder,
                ];

                do {
                    $result = $client->listObjectsV2($params);
                    if (!empty($result['Contents'])) {
                        foreach ($result['Contents'] as $object) {
                            $key = $object['Key'];

                            // Only mark for deletion if it's NOT in the keep list
                            if (!in_array(basename($key), $keepList)) {
                                $objectsToDelete[] = ['Key' => $key];
                            }
                        }
                    }

                    // Continue if there are more objects
                    $params['ContinuationToken'] = $result['NextContinuationToken'] ?? null;

                } while (!empty($result['IsTruncated']));

                // Delete the unwanted objects in batches (max 1000 per batch)
                if (!empty($objectsToDelete)) {
                    $deleteChunks = array_chunk($objectsToDelete, 1000);
                    foreach ($deleteChunks as $chunk) {
                        $client->deleteObjects([
                            'Bucket' => $bucket,
                            'Delete' => ['Objects' => $chunk],
                        ]);
                    }

        //            $this->setLog("Deleted " . count($objectsToDelete) . " objects",true);
                } else { 
        //            $this->setLog("No objects to delete.",true); 
                }

            } catch (AwsException $e) {
                 $this->setLog( $e->getMessage(),true);  
            }
   }
      
   
  }

    function initStorage(){
        
        
        $storageName = $this->loadSetting('storageName');
        
        if($storageName == 'S3'){
            if(!defined('STORAGE')){
                
                if(!empty($storageName)){ 
                        define('STORAGE',array(
                            'name' => $storageName,
                            'domain' => S3['domain'],
                            'bucket' => S3['bucket'],
                            'endpoint' => S3['endpoint'],
                            'accesskey' => S3['accesskey'],
                            'secretkey' => S3['secretkey'], 
                        ));   
                }
            } 
        }
         
       
    }
    
    function initS3Client(){
        if (!defined('STORAGE')) return null;
        
        $client = new Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => 'wjv-1',
                'endpoint' => STORAGE['domain'],
                'use_path_style_endpoint' => false, // Configures to use subdomain/virtual calling format.
                'credentials' => [
                        'key'    => STORAGE['accesskey'],
                        'secret' => STORAGE['secretkey'],
                    ],
        ]);
            
        return $client;

    }
    
    function putS3Object($payload, $pkey = '',$client = ''){
         
        // kalo gk ad filenya direturn, 
        // utk edit biasanya jg tdk ad tmp_name nya, tp namenya tetep ad
        
        // harus ad nama domain
        if(empty(DOMAIN_NAME)) return;
        //if(empty($payload['tmp_name'])) return; /gk bisa karena ada kalanya kita kirim body
        
        if(empty($client)) $client = $this->initS3Client();
         
//         $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        
//         $this->uploadProgressOpt = array();
//         $this->uploadProgressOpt ['start'] = true;
//         $this->uploadProgressOpt ['hashkey'] = md5($tablekey.$pkey.$payload['destinationFile'].time().rand(100,1000));
//         $this->uploadProgressOpt ['pkey'] = $pkey;
//         $this->uploadProgressOpt ['tablekey'] = $tablekey;
//         $this->uploadProgressOpt ['file'] = $payload['destinationFile'];
        
         $acl = (!empty($payload['acl'])) ? $payload['acl'] : 'private';
         
         $arrContent = array( 
        
             'Bucket' => STORAGE['bucket'],
             'Key'    => DOMAIN_NAME.'/'.$payload['destinationFile'],  
             'ACL'    =>  $acl, // set private by default 
//             '@http' => [
//                        'progress' => function ($downloadTotalSize, $downloadSizeSoFar, $uploadTotalSize, $uploadSizeSoFar) {
//                            
//                        }
//            ]
         );
        

        if (isset($payload['tmp_name']) && !empty($payload['tmp_name']))
                $arrContent['SourceFile'] = $payload['tmp_name'];
            
        if (isset($payload['type']) && !empty($payload['type']))
                $arrContent['ContentType'] = $payload['type'];
            
        if (isset($payload['body']) && !empty($payload['body']))
                $arrContent['Body'] = $payload['body'];
            
        $client->putObject($arrContent); 
       
    }
    
	function uploadQueuingFile($pkey,$arrQueue = array()){
		
        // kalo die, error ketika trucking invocie create miantenance di TKJ
        if(empty($pkey) || !is_numeric($pkey)) return;
        
        if($this->useStorage('S3')){ 
              
            $arrayToJs = array();
            
            // 1. cek file sebenarnya yg perlu disave
            // 2. delete yg ada dimapping tp gk ad di database, hapus
            // 3. upload file baru
            
            $client = $this->initS3Client();
             
            // $arrQueue bisa diisi dari ajax, ketika user update file pada saat transaksi sudah konfirmasi
            if(!empty($arrQueue)) 
                $this->uploadQueue = $arrQueue;
            
            try{  
    
                $arrFilesToKeep = array();
                foreach($this->uploadQueue as $key=>$queue){ 
                    if(empty($queue['name'])) continue; // harusnya tdk terjadi
                    
                    // kalo ad validasi ini ada masalah, file2 lama gk kedelete 
//                    if(!empty($queue['tmp_name'])) continue; // utk file baru, buat jaga2 kalo nama filenya sama (nimpa)
                    
                    array_push($arrFilesToKeep, array('folder' => $queue['uploadFolder'], 'fileName'=> $queue['name']));
                }
                 
                $this->removeDeletedFilesS3($pkey,$arrFilesToKeep);
                    
                 // mulai upload file  
                 foreach($this->uploadQueue as $key=>$queue){ 
                     // kalo gk upload apa2.. lewatin

                    if (empty($queue['name']) || empty($queue['tmp_name'])) continue; 
                    // baru upload ulang, nanti lihat header sama detail sama gk stylenya 
                     
                    $queue['destinationFile'] =  $queue['uploadFolder'].$pkey.'/'.$queue['name'];
                    
                    $this->putS3Object($queue,$pkey); 
                }

                  $this->addErrorList($arrayToJs, true,  $this->lang['dataHasBeenSuccessfullyUpdated']);   
                 
            } catch(Exception $e){ 
                    $this->addErrorList($arrayToJs, false, $e->getMessage());
            }	

            return $arrayToJs;
 
        }else{
            
            	// copy file
                $deleteDestination = true;

                // prioritaskan file yg dikirim.
                $useObjQueing = (!empty($arrQueue)) ? false : true;

                $arrFiles = (!$useObjQueing) ? $arrQueue : $this->uploadQueue;
                
                foreach($arrFiles as $key=>$queue){ 
                    $token = $queue['token'];
                    $fileName = $queue['fileName'];
                    $path = $queue['uploadFolder'];

                    $sourcePath = $this->uploadTempDoc.$path.$token;
                    $destinationPath = $this->defaultDocUploadPath.$path;

                    if(!is_dir($sourcePath)) continue;
                    if(!is_dir($destinationPath)) mkdir ($destinationPath,  0755, true);

                    $destinationPath .= $pkey;  

                    $this->uploadFile($sourcePath, $destinationPath, $fileName, '',$deleteDestination);  
                    $deleteDestination = false;

                }

                // clear kalo files dari queue class, bukan parameter
                if ($useObjQueing)
                    $this->uploadQueue = array();
        }
	
	}
    
    function afterCommitChangeStatus($pkey){
        
    }
    
    function afterCommitDelete($rs){
        
    }
    
   /* function getCurrentData($id){
        if (!isset($this->arrData)) return;
                    
        $returnArr = array();
        
        $dataset = $this->arrData;
        $tableName = $this->tableName;

        $sql = 'select * from '.$tableName.' where pkey = ' . $this->oDbCon->paramString($id);
        $rs = $this->oDbCon->doQuery($sql);
        
        $returnArr[$this->tableName] = $rs; 
        
        
        $hasDetail = (isset($dataset['pkey'][1]['dataDetail'])) ? true : false; 
        if($hasDetail){
        
              $detailOptions = $dataset['pkey'][1]['dataDetail']; // ini bisa balikinnya array. jd standartkan saja
             // if only have 1 dimension array
             if (!isset($detailOptions[0])){ 
                $temp = $detailOptions;
                $detailOptions = array();
                array_push($detailOptions, $temp);  
             }    

             // utk setiap detail 
             foreach($detailOptions as $detailOption) {
                 
             }

            
        }
        
    }
    
    
    function getCurrentDataDetail($dataStructure){
        while(isset($dataStructure['dataDetail'])){
  
            $detailOptions = $data['dataDetail'];

            // if only have 1 dimension array
            if (!isset($detailOptions[0])){ 
                $temp = $detailOptions;
                $detailOptions = array();
                array_push($detailOptions, $temp);  
            }    

            foreach($detailOptions as $detailOption) { 
              //$this->updateDataDetail($arrParam, $data[0],$detailOption);   
                
            }
        }
    }
    */
    
    function saveAndProceed($arrParam){
        $arrayToJs =  array();
        
        if ($this->isTransaction){  
            if (isset($arrParam['hidSaveAndProceed']) && !empty($arrParam['hidSaveAndProceed'])){ 
               $pkey = $arrParam['pkey'];  
               $rsHeader = $this->getDataRowById($pkey); 
                
              // buaat jaga kalo ada staatus yg diskip, mislny Pengajuan utk TCO
               $rsNextStatus = $this->getNextAvailableStatus($rsHeader[0]['statuskey']);
               $arrayToJs = $this->changeStatus($pkey,$rsNextStatus[0]['pkey']);  
            }
        }
 
		return $arrayToJs;
    }
    
    function getCustomCodeResetType (){
		$sql = 'select * from '.$this->tableCustomCodeResetType ;
		return $this->oDbCon->doQuery($sql); 
	}
    
    function getDefaultData(){
        // pake searchdatarow saja, agar gk kena warehouseCriteria
        $rs = $this->searchDataRow(array($this->tableName.'.pkey'),
                                    ' and '.$this->tableName.'.systemVariable = 1',
                                   ' order by pkey asc limit 1'
                                  );
        return $rs[0]['pkey'];
    }
    
    

    /* REPORT */

    function generateReportRow($data,$templateDataStructure,$isHeader = false, $headerRow = false){
 
        $colIndex = 0;
		$totalFreezeCol = (isset($data['totalFreezeCol']))? $data['totalFreezeCol'] : 2;
			
        $arrData = array(); 
        $arrSubtotal = array(); 
        
        if(!isset($data['data']))
             $data['data'] = array();
        
        $datarow = $data['data'];
        $stylerow = (isset($data['style'])) ? $data['style'] : array() ;
        
        $expandableClass = (isset($datarow['_detail_']) && !empty($datarow['_detail_'])) ? 'expandable-report-row' : '';
        
        $class = ($isHeader) ? 'table-header' : 'rewrite-row '.$expandableClass;
        $temptablerow  = '<tr class="'.$class.'">'; 

        foreach ($templateDataStructure as $index=>$el){ 
    
            $dbfield = (isset($el['dbfield']) && !empty($el['dbfield'])) ? $el['dbfield'] : '';
            
            if ( (isset($el['autoNumber'])) &&  $el['autoNumber'] ){ 
                $this->reportRowsNumber[$el['title']] = (isset($this->reportRowsNumber[$el['title']])) ?  $this->reportRowsNumber[$el['title']]+1 : 1; 
                $value = $this->reportRowsNumber[$el['title']];
            }else{   
                // kalo gk ad fieldnya
                // terjadi khusus laporan biaya SPK
                if (!isset($datarow[$dbfield])){
                    $datarow[$dbfield] = '';
                    
                    if (isset($el['format'])){
                         switch($el['format']){
                                case 'integer' : 
                                case 'number' : $datarow[$dbfield] = 0;
                                                break; 
                                case 'date' : 
                                case 'datetime' : $datarow[$dbfield] = DEFAULT_EMPTY_DATE; 
                                                break; 
                            }
                    } 
                }
                
                $value = ($isHeader) ? $el['title'] : $datarow[$dbfield]; 
            }
            
                
            $excelValue = str_replace('<br>',chr(13),$value); // Unformatted Value for Excel
            $excelValue = html_entity_decode ($excelValue);
                
            $style = array();
            $textalign = '';

            if (isset($el['format'])){
                switch($el['format']){
                        
                    case 'accounting' :  
                                    //  harus ad bedanya antara 0 atau '' , utk trialBalance agar gk muncul angka 0
                                    if(!is_numeric($value)) break;
                                        
                                    $arrSubtotal[$index] = $value;
                                    if(!$isHeader)  
                                        $value =  ($value >= 0) ? $this->formatNumber($value) :  '('.$this->formatNumber(abs($value)).')'; 
                                   
                                    $textalign = 'text-align:right;';
                                    break;
                        
                    case 'integer' : $arrSubtotal[$index] = $value;
                                    $value = ($isHeader) ? $value : $this->formatNumber($value,0);
                                    $textalign = 'text-align:right;';
                                    break;
                        
                    case 'decimal' : $arrSubtotal[$index] = $value;
                                    $value = ($isHeader) ? $value : $this->formatNumber($value,2);
                                    $textalign = 'text-align:right;';
                                    break;
                        
                    case 'autodecimal' : $arrSubtotal[$index] = $value;
                                    $value = ($isHeader) ? $value : $this->formatNumber($value,-2);
                                    $textalign = 'text-align:right;';
                                    break;
                        
                    case 'number' : $arrSubtotal[$index] = $value;
                                    $value = ($isHeader) ? $value : $this->formatNumber($value,-3);
                                    $textalign = 'text-align:right;';
                                    break;
                        
                    case 'date' :   $returnOnEmpty = (isset($el['returnOnEmpty'])) ? $el['returnOnEmpty'] : array();  
                                    $value = ($isHeader) ? $value : $this->formatDBDate($value,'',$returnOnEmpty);
                                    $textalign = 'text-align:center;';
                                    break; 
					case 'datetime' : $returnOnEmpty = (isset($el['returnOnEmpty'])) ? $el['returnOnEmpty'] : array();  
									$value = ($isHeader) ? $value : $this->formatDBDate($value,'d / m / Y H:i',$returnOnEmpty);
                                    $textalign = 'text-align:center;';
                                    break; 
                    case 'monthYear' : $value = ($isHeader) ? $value : $this->formatDBDate($value,'F Y');
                                    $textalign = 'text-align:center;';
                                    break;
                   // case 'string' : $excelValue = '\''.$value;
                }
            }

            //============================ overwrite $textalign
            $textalign =  (isset($el['align'])) ? 'text-align:'.$el['align'].';' : $textalign;
            array_push($style,$textalign);

            $width = ($isHeader && isset($el['width']) && !empty($el['width'])) ? 'width:'.$el['width'].';': '';
            array_push($style,$width); 
            
            $textColor = '';
            //============================ text color
            // structure default
            if (isset($el['textColor']) && !empty($el['textColor'])) 
                $textColor = str_replace('#','',$el['textColor']); 
             
            // overwrite detail
            if (isset($el['dbfield']) && isset($stylerow[$el['dbfield']]['textColor']) && !empty($stylerow[$el['dbfield']]['textColor'])) {
                $textColor = str_replace('#','',$stylerow[$el['dbfield']]['textColor']);  
            }
                
            if(!empty($textColor)){
                $arrData[$index]['excelStyle']['textColor'] = $textColor;   
                $textColor = 'color:#'.$textColor.';';
                array_push($style,$textColor);
            }
             
            // bg color
            $bgColor = '';
            if (isset($el['dbfield']) && isset($stylerow[$el['dbfield']]['backgroundColor']) && !empty($stylerow[$el['dbfield']]['backgroundColor'])) {
                $bgColor = str_replace('#','',$stylerow[$el['dbfield']]['backgroundColor']);  
            } 
            
            if(!empty($bgColor)){
                $arrData[$index]['excelStyle']['backgroundColor'] = $bgColor;   
                $bgColor = 'background-color:#'.$bgColor.'; border-radius: 0.2em; padding:0.1em;';
                //array_push($style,$bgColor);
            }
             
            
            // font-weight 
            if (isset($el['dbfield']) && isset($stylerow[$el['dbfield']]['fontWeight']) && !empty($stylerow[$el['dbfield']]['fontWeight'])) { 
                $fontWeight = $stylerow[$el['dbfield']]['fontWeight'];
                //$arrData[$index]['excelStyle']['textColor'] = $textColor;   // blm tau diexcel gmana, nanti saja
                $fontWeight = 'font-weight:'.$fontWeight.';';
                array_push($style,$fontWeight);
            }
            
                  
            $style = trim(implode(' ',$style));
            
            if (isset($el['style']) && !empty($el['style'])) 
                $style .= $el['style'];
            
            $style =  (!empty($style)) ? 'style="'.$style.'"' : ''; 
            
            $class =  (isset($el['class'])) ? $el['class'] : '';
            if ($colIndex == ($totalFreezeCol-1))  // sebelumnya nilainya 1
                $class .=  'freeze-pane-border';
            
            $class =  (!empty($class)) ? 'class="'.$class.'"' : ''; 
            
            $value = str_replace(chr(13),'<br>',$value);
            
            if ($headerRow && $colIndex < $totalFreezeCol){ // sebelumnya nilainya 2
                $temptablerow  .= '<th  '.$class.' '.$style.'>'.$value.'</th>';   
                $colIndex++;
            }else{ 
                
              /*  if(isset($el['detail'])){
                    $newTable = '<div class="div-table" style="width:100%; "><div class="div-table-row">';
                    foreach($el['detail'] as $detailGroup){
                        $newTable .= '<div class="div-table-col-03" style="background-color:#dedede;">ZZZZ ZZZ ZZZZ ZZ ZZZZZ ZZZZ'.$value.'</div>';
                    }
                    $newTable .= '</div></div>';
                    
                    $value = $newTable;
                } */
                 
                 if(!empty($bgColor))
                  $value = '<div style="'.$bgColor.'">'.$value.'</div>';
                
                 $temptablerow  .= '<td '.$class.' '.$style.'>'.$value.'</td>'; 
            }
 
            $arrData[$index]['value'] = $value;
            $arrData[$index]['excelValue'] = $excelValue;  

        }  

        $temptablerow  .= '<td class="dummy-grow-col"></td>';    
        $temptablerow  .= '</tr>';
         
        return array('html' => $temptablerow, 'data' => $arrData, 'subtotal' => $arrSubtotal);
    }
    
    function formatReportRows($data,$arrTemplate){ 
        $arrData = array(); // setiap dimensi menunjukan level outline
        $arrSubtotal = array(); // setiap dimensi menunjukan level outline
        $arrDataHeader = array();
        $arrDataDetail = array();
        $arrSubtotalDetail = array();
        
        $datarow = $data['data'];
		$totalFreezeCol = (isset($data['totalFreezeCol']))? $data['totalFreezeCol'] : 2;
		
        
        $stylerow = (isset($data['style'])) ? $data['style'] : array();  
        
        $headerDataStructure = $arrTemplate[0]['dataStructure']; 
       /* $headerDataStructure = array();
        foreach ($arrTemplate[0]['dataStructure'] as $row){  
            if (isset($row['detail'])){
                 foreach ($row['detail'] as $detailRow){ 
                        array_push($headerDataStructure, $detailRow);  
                 }
            }else{
                array_push($headerDataStructure, $row);  
            }
        }*/
        
        
        //$this->setLog($arrTemp);
        $temptablerow = '';

        // header
        $returnValue =  $this->generateReportRow(array('data' => $datarow, 'style' => $stylerow, 'totalFreezeCol' => $totalFreezeCol),$headerDataStructure,false,true);
        $temptablerow .= $returnValue['html'];
        
        array_push($arrDataHeader, $returnValue['data']); // utk menstandarkan dimensi array ketika export 
        array_push($arrData,$arrDataHeader);
        
        array_push($arrSubtotal,$returnValue['subtotal']); // utk menstandarkan dimensi array
        
        // detail
        $tempDetail = ''; 
        if (isset($datarow['_detail_']) && !empty($datarow['_detail_'])){ 
            
            $datarowDetail = $datarow['_detail_'];
            $rsDetail = $datarowDetail['data'] ?? [];
            $arrStyle =  (isset($datarowDetail['style'])) ? $datarowDetail['style'] : array() ;
            
            $detailTemplate = $datarowDetail['arrTemplate']['dataStructure'];
            $tableWidth = (isset($datarowDetail['arrTemplate']['reportWidth']) && !empty($datarowDetail['arrTemplate']['reportWidth'])) ? $datarowDetail['arrTemplate']['reportWidth'] : '100%';
              
            // table detail title
            $returnValue =  $this->generateReportRow(null,$detailTemplate,true);
            $tempDetail .= $returnValue['html'];
             
            for($i=0;$i<count($rsDetail);$i++){ 
                
                $arrRowStyle = (isset($arrStyle[$i])) ? $arrStyle[$i] : array() ;
                 
                $returnValue =  $this->generateReportRow(array('data' => $rsDetail[$i], 'style' => $arrRowStyle, 'totalFreezeCol' => $totalFreezeCol),$detailTemplate);
                $tempDetail .= $returnValue['html'];
                
                // $this->setLog($returnValue['subtotal']['totalcogs']);
                // count subtotal for each col
                $datarowDetail['arrTemplate']['total'] = $this->arraySum($datarowDetail['arrTemplate']['total'], $returnValue['subtotal']);

                array_push($arrDataDetail, $returnValue['data']); 
                array_push($arrSubtotalDetail, $returnValue['subtotal']); 
            } 
            
            // footer utk detail
            $tempDetail .= $this->formatReportFooterRows($datarowDetail['arrTemplate']['total'],$detailTemplate,false);
  
            $tempDetail = '<table class="detail-table" style="table-layout:fixed;  width:'.$tableWidth.'; ">' . $tempDetail .'</table>' ;
            array_push($arrData,$arrDataDetail);
            array_push($arrData,$arrSubtotalDetail);
        }
          
        $temptablerow .= '<tr class="detail-row rewrite-row">';
        $temptablerow .= '<td class="table-detail-panel" style="width:10px"></td>';
        $temptablerow .= '<td  colspan="'.(count($headerDataStructure)).'" class="table-detail-panel">'; // gk perlu minus 1 karena ad dummy td dibelakang
        $temptablerow .= $tempDetail; 
        $temptablerow .= '</td>';
        $temptablerow .= '</tr>';  
  
        return array('html' => $temptablerow, 'data' => $arrData, 'subtotal' => $arrSubtotal);
    } 

    function generateReport($POST, $reportContent, $arrTemplate, $data, $arrFilterInformation,$tableHeader='', $footnote = '', $arrDataAIJSON = array()){ 
  
//        $totalFreezeCol = (isset($POST['hidTotalFreezeCol'])) ? $POST['hidTotalFreezeCol'] : 2;
		
        $arrHeaderTemplate = $arrTemplate[0];     
        
		if (empty($reportContent)) 
			$reportContent = '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
     
        // total rows
        $reportContent .= $this->formatReportFooterRows($arrHeaderTemplate['total'],$arrHeaderTemplate['dataStructure']);

		// sementara utk compability
		$dataToExport = (isset($data['dataToExport'])) ? $data['dataToExport'] : $data;
		
        $arrTemplate[0]['dataToExport'] = $dataToExport;
        $arrTemplate[0]['filterInformation'] = $arrFilterInformation;
        $arrTemplate[0]['footnote'] = $footnote;
  
        // ============= save ke file agar export excel gk perlu narik ulang
        $fileDataPath = '';
        // data kosong tetep harus dibuat filenya...
        // if(!empty($dataToExport)){ 
            
            //$start_time = microtime(TRUE);
        
        
            $path = $this->uploadTempDoc.'export-data/';  
            if (!is_dir($path))
                mkdir ($path,  0755, true);

            $fileDataName = time().$this->userkey.rand();
            $fileDataPath = fopen($path.$fileDataName, "w");

            $arr = array();
            $arr['arrTemplate'] = $arrTemplate;
            $arr['module'] = (isset($POST['module'])) ? $POST['module'] : '';
            //$arr['arrTemplate'] = $arrTemplate;
        
            $contentToWrite = json_encode($arr);
            //$contentToWrite = gzcompress($contentToWrite);
        
            fwrite($fileDataPath, $contentToWrite);

            //$this->setLog($this->getPerformanceLog($start_time),true);
        
            fclose($fileDataPath);
        

            // utk AI 
            if(!empty($arrDataAIJSON)){ 
                $path = $this->uploadTempDoc.'data-ai/';  
                if (!is_dir($path)) mkdir ($path,  0755, true);
                $fileDataPath = fopen($path.$fileDataName, "w");  
                fwrite($fileDataPath, $arrDataAIJSON); 
                fclose($fileDataPath);
            }

       // }
        // ============================
        
        if (isset($POST['hidExportExcel']) && !empty($POST['hidExportExcel'])){  
            //$this->setLog("generate from baseclass",true);
            $excel = new Excel();
            $arrExportParam = array();
            $arrExportParam['exportType'] = $POST['hidExportExcel'];
            $arrExportParam['module'] = $POST['module'];
            $excel->exportToSave($arrTemplate,$arrExportParam);  
        }else{  
            echo json_encode(array('fileData' => $fileDataName, 
								   'filterInformation' => $arrFilterInformation, 
								   'content' => $reportContent,
								   'header' => $tableHeader,
								   'footnote' => $footnote,
								   'rs' => (isset($data['rs'])) ? $data['rs'] : array()
								   /*, 'tableHeader' => $arrHeaderTemplate['dataStructure']*/));
            die;
        }

    }
    

function getPerformanceLog($start_time){ 
    $end_time = microtime(TRUE); 
    $time_taken = $end_time - $start_time; 
    $time_taken = round($time_taken,5); 

    $var = 'Page generated in '.$time_taken.' seconds.';

    /* Currently used memory */
    $mem_usage = memory_get_usage() / 1024;

    /* Peak memory usage */
    $mem_peak = memory_get_peak_usage() / 1024;


   $varMemory = 'The script is now using: ' . number_format($mem_usage) . ' KB of memory'.chr(13);
   $varMemory .= 'Peak usage: ' . number_format($mem_peak) . ' KB of memory.'.chr(13);

   $var .= chr(13).$varMemory;

    return array('msg' => $var, 'timeTaken' => $time_taken, 'memoryPeak' => $mem_peak);
}

    
    function formatReportFooterRows($arrSubtotal,$headerDataStructure, $isHeader = true){
            
            $class = ($isHeader) ? 'subtotal rewrite-row': 'subtotal' ;
        
            $html = '<tr class="'.$class.'"> ';   
            foreach ($headerDataStructure as $index=>$item){ 
              $total = '';
              if (isset($item['calculateTotal']) && $item['calculateTotal']){  
                   $decimal = ($item['format'] == 'decimal') ? 2 : -1; 
                   $total = (isset($arrSubtotal[$index])) ?  $this->formatNumber($arrSubtotal[$index],$decimal) : 0;
              }
                   
              $html .= ($total <> '') ? '<td style="text-align:right;">'. $total .'</td>' : '<td></td>';   
            }
                  
            $html .= '<td class="dummy-grow-col"></td>';
        
            $html .= '</tr>'; 
            
            return $html;
    }
    
    function arraySum($arr, $newArr){
        
        foreach ($newArr as $index=>$value){   
            $value = (float) $value;
            $arr[$index] = (!isset($arr[$index])) ? $value : $arr[$index] + $value;  
        }
        
        return $arr;
    }
    
    // for POS print
     function formatString($text,$length,$opt=array()){ 
         
        $space = ' '; 
         
        $align = (isset($opt['align']) && !empty($opt['align']) )  ? $opt['align'] : 'left';
        $split = (isset($opt['split']) && !empty($opt['split']) )  ? $opt['split'] : 0;
        //$offset = (isset($opt['offset']) && !empty($opt['offset']) )  ? $opt['offset'] : 0;
          
        $arrText = array();
        if($split > 0) 
             $arrText = str_split($text, $length); 
        else
            array_push($arrText, $text);
         
         
        $arrData = array();
         
        foreach ($arrText as $text) { 
            
            $data = ""; 
            $text = trim($text);
            //$this->setLog('x'.$text.'x');   
            
            $textlength =  strlen($text);
		
            if($textlength>$length)
             $text = substr($text,0,$length);  

            // cek ulagn apakah ada spasi setelah di substring
            $text = trim($text);
            $textlength =  strlen($text);

            if($align=="left"){
                $data.=$text;
                $data.=str_repeat($space,$length-$textlength);
            }else if($align=="right"){
                $data.=str_repeat($space,$length-$textlength);
                $data.=$text;
            }else{
                 $midspace =  floor(($length-$textlength) / 2);
                 $data.=str_repeat($space,$midspace); 
                 $data.=$text; 
                 //$this->setLog('-'.$data.'-');    
            }
            
           array_push($arrData, $data);
        }
 
         
		return implode("\n",$arrData);
	}
    
    function updateDetailRows($refkey,$arrParam, $dataset,  $arrOptions = array()){
        // $arrPostedKeys -> array detailkeys
        
        $tableName = (isset($arrOptions['tableName']) && !empty($arrOptions['tableName'])) ? $arrOptions['tableName'] : $this->tableNameDetail ;
        $reffield = (isset($arrOptions['refField']) && !empty($arrOptions['refField'])) ? $arrOptions['refField'] : 'refkey' ; 
        
        // patokan key detail, sementara selalu 'pkey';
        $detailkey = $dataset['pkey'][0];
        $arrKeys = array_keys($dataset);
        
        $totalField = count($arrKeys); 
        
         // existing detail keys
        $sql = 'select pkey from '.$tableName.' where '.$reffield.' = ' . $this->oDbCon->paramString($refkey); 
        $rsDetail = $this->oDbCon->doQuery($sql); 
        $arrDetailKeys = array_column($rsDetail,'pkey');
        
        // posted detail keys 
        $arrPostedKeys = $arrParam[$detailkey];
        
        // delete data yg sudah diremove di UI / atau yg nilainya kosong
        $deletedKey = array_diff($arrDetailKeys, $arrPostedKeys);
        if (!empty($deletedKey)){ 
            $deletedKey = implode(',',$deletedKey); 
            $sql = 'delete from '.$tableName.' where pkey in ('.$deletedKey.') ' ;  
            $this->oDbCon->execute($sql);
        }
        
        
         // insert / update
        
        $totalRows = count($arrPostedKeys);
        for($i=0;$i<$totalRows;$i++){  

            $arrCol = array(); 
            $arrVal = array(); 
            
            $hasEmptyValue = false; 
            $prepareFor = (in_array($arrPostedKeys[$i], $arrDetailKeys)) ? UPDATE_DATA : INSERT_DATA ; 

            for($j=0;$j<$totalField;$j++){   
                    if ($arrKeys[$j] == 'pkey')
                        continue;

                    $elName = $dataset[$arrKeys[$j]][0]; 
                
                    $dbField = $arrKeys[$j];  
                
                    // kalo ad parameter kedua, cek array atau bkn
                    $dataType = '';
                    $optiontemp = (isset($dataset[$arrKeys[$j]][1])) ? $dataset[$arrKeys[$j]][1] : '';  
                 
                    if (is_array($optiontemp)) 
                        $dataType = (isset($optiontemp['datatype'])) ? $optiontemp['datatype'] : '';
                    else
                        $dataType = $optiontemp;
                              
                    $value = ($dataType == 'ref' ) ? $arrParam[$elName] : $arrParam[$elName][$i];   
                    
                    $ignored = false;
                    //$ignored = (isset($arrParam['_ignored_'][$elName][$i]) && $arrParam['_ignored_'][$elName][$i] ) ? $arrParam['_ignored_'][$elName][$i] : false;
                
                   // $this->setLog($elName);
                
                    // kalo gk perlu diupdate, gk perlu diapa2in
                    if (!$ignored){
                        if (isset($optiontemp['mandatory']) && $optiontemp['mandatory'] && empty($value) ){ 
                            $hasEmptyValue = true;
                            break;
                        }

                        $value = $this->formatDataForUpdate($value, $dataType); 
                        
                        if ($prepareFor == INSERT_DATA){
                            array_push($arrCol, $dbField);
                            array_push($arrVal, $value); 
                        }else{ 
                            $queryString = $dbField . ' = ' .  $value ;
                            array_push($arrCol,$queryString);  
                        }
                    }else{
                         //    $this->setLog('tet');
                    }
                   
            }
            
            $sql = '';
            if ($prepareFor == INSERT_DATA && !$hasEmptyValue) { 
               $sql = 'insert into '.$tableName.' ('.implode(',',$arrCol).') values ('.implode(',',$arrVal).')';   
            }else if ($prepareFor == UPDATE_DATA ){ 
               if (!$hasEmptyValue)
                    $sql = 'update '.$tableName.' set '. implode(',',$arrCol).' where pkey = ' . $arrPostedKeys[$i];
                else
                    $sql = 'delete from '.$tableName.' where pkey = ' . $arrPostedKeys[$i];
                
            }
                
            if (!empty($sql))
                $this->oDbCon->execute($sql);
            
            // reupdate detailkey variable
           /* $sql = 'select pkey from '.$tableName.' where '.$reffield. '=' . $this->oDbCon->paramString($refkey); 
            $rsDetail = $this->oDbCon->doQuery($sql); 
            $arrDetailKeys = array_column($rsDetail,'pkey');
            
            for($i=0;$i<count($arrDetailKeys);$i++){
                $this->setLog($arrDetailKeys[$i]);
                $arrParam[$detailkey][$i] = $arrDetailKeys[$i];
            }*/
        }
        
    }
    
function sayNumberInEnglish($number){
    $number = floatVal($number);
    
    list($integer, $fraction) = explode(".", (string) $number);

    $output = "";

    if ($integer[0] == "-")
    {
        $output = "negative ";
        $integer    = ltrim($integer, "-");
    }
    else if ($integer[0] == "+")
    {
        $output = "positive ";
        $integer    = ltrim($integer, "+");
    }

    if ($integer[0] == "0")
    {
        $output .= "zero";
    }
    else
    {
        $integer = str_pad($integer, 36, "0", STR_PAD_LEFT);
        $group   = rtrim(chunk_split($integer, 3, " "), " ");
        $groups  = explode(" ", $group);

        $groups2 = array();
        foreach ($groups as $g)
        {
            $groups2[] = $this->convertThreeDigit($g[0], $g[1], $g[2]);
        }

        for ($z = 0; $z < count($groups2); $z++)
        {
            if ($groups2[$z] != "")
            {
                $output .= $groups2[$z] . $this->convertGroup(11 - $z) . (
                        $z < 11
                        && !array_search('', array_slice($groups2, $z + 1, -1))
                        && $groups2[11] != ''
                        && $groups[11][0] == '0'
                            ? " and "
                            : ", "
                    );
            }
        }

        $output = rtrim($output, ", ");
    }

    if ($fraction > 0)
    {
        $output .= " point";
        for ($i = 0; $i < strlen($fraction); $i++)
        {
            $output .= " " . $this->convertDigit($fraction[$i]);
        }
    }

    return $output;
}

function convertGroup($index)
{
    switch ($index)
    {
        case 11:
            return " decillion";
        case 10:
            return " nonillion";
        case 9:
            return " octillion";
        case 8:
            return " septillion";
        case 7:
            return " sextillion";
        case 6:
            return " quintrillion";
        case 5:
            return " quadrillion";
        case 4:
            return " trillion";
        case 3:
            return " billion";
        case 2:
            return " million";
        case 1:
            return " thousand";
        case 0:
            return "";
    }
}

function convertThreeDigit($digit1, $digit2, $digit3){
    $buffer = "";

    if ($digit1 == "0" && $digit2 == "0" && $digit3 == "0")
    {
        return "";
    }

    if ($digit1 != "0")
    {
        $buffer .= $this->convertDigit($digit1) . " hundred";
        if ($digit2 != "0" || $digit3 != "0")
        {
            $buffer .= " and ";
        }
    }

    if ($digit2 != "0")
    {
        $buffer .= $this->convertTwoDigit($digit2, $digit3);
    }
    else if ($digit3 != "0")
    {
        $buffer .= $this->convertDigit($digit3);
    }

    return $buffer;
}

function convertTwoDigit($digit1, $digit2){
    if ($digit2 == "0")
    {
        switch ($digit1)
        {
            case "1":
                return "ten";
            case "2":
                return "twenty";
            case "3":
                return "thirty";
            case "4":
                return "forty";
            case "5":
                return "fifty";
            case "6":
                return "sixty";
            case "7":
                return "seventy";
            case "8":
                return "eighty";
            case "9":
                return "ninety";
        }
    } else if ($digit1 == "1")
    {
        switch ($digit2)
        {
            case "1":
                return "eleven";
            case "2":
                return "twelve";
            case "3":
                return "thirteen";
            case "4":
                return "fourteen";
            case "5":
                return "fifteen";
            case "6":
                return "sixteen";
            case "7":
                return "seventeen";
            case "8":
                return "eighteen";
            case "9":
                return "nineteen";
        }
    } else
    {
        $temp = $this->convertDigit($digit2);
        switch ($digit1)
        {
            case "2":
                return "twenty-$temp";
            case "3":
                return "thirty-$temp";
            case "4":
                return "forty-$temp";
            case "5":
                return "fifty-$temp";
            case "6":
                return "sixty-$temp";
            case "7":
                return "seventy-$temp";
            case "8":
                return "eighty-$temp";
            case "9":
                return "ninety-$temp";
        }
    }
}

function convertDigit($digit){
    switch ($digit) {
        case "0":
            return "zero";
        case "1":
            return "one";
        case "2":
            return "two";
        case "3":
            return "three";
        case "4":
            return "four";
        case "5":
            return "five";
        case "6":
            return "six";
        case "7":
            return "seven";
        case "8":
            return "eight";
        case "9":
            return "nine";
    }
}
    
/*    function sayNumberInEnglish ($num){
         
        $num = (int) $num;    // make sure it's an integer

        if ($num < 0)
          return "negative".convertTri(-$num, 0);

        if ($num == 0)
          return "zero";
        
        $result = $this->convertTri($num, 0); 
        return trim($result);
        
    } 
    function convertTri($num, $tri) {
          
        $ones = array(
         "",
         " one",
         " two",
         " three",
         " four",
         " five",
         " six",
         " seven",
         " eight",
         " nine",
         " ten",
         " eleven",
         " twelve",
         " thirteen",
         " fourteen",
         " fifteen",
         " sixteen",
         " seventeen",
         " eighteen",
         " nineteen"
        );

        $tens = array(
         "",
         "",
         " twenty",
         " thirty",
         " forty",
         " fifty",
         " sixty",
         " seventy",
         " eighty",
         " ninety"
        );

        $triplets = array(
         "",
         " thousand",
         " million",
         " billion",
         " trillion",
         " quadrillion",
         " quintillion",
         " sextillion",
         " septillion",
         " octillion",
         " nonillion"
        );
        

          // chunk the number, ...rxyy
          $r = (int) ($num / 1000);
          $x = ($num / 100) % 10;
          $y = $num % 100;

          // init the output string
          $str = "";

          // do hundreds
          if ($x > 0)
           $str = $ones[$x] . " hundred";

          // do ones and tens
          if ($y < 20)
           $str .= $ones[$y];
          else
           $str .= $tens[(int) ($y / 10)] . $ones[$y % 10];

          // add triplet modifier only if there
          // is some output to be modified...
          if ($str != "")
           $str .= $triplets[$tri];

          // continue recursing?
          if ($r > 0)
           return $this->convertTri($r, $tri+1).$str;
          else
           return $str;
         }*/

    function retrieveReadonlyDataRow(&$arrParam, $rsDetail,$arrDataStructure, $fieldRefKey , $detailPkeyEl ='hidDetailKey' ){
          
        if (empty($rsDetail))
            return;
        
         //$this->setLog('$detailPkeyEl ==> ' . $detailPkeyEl);
         $arrDetailPkey = $arrParam[$detailPkeyEl]; 
         //$this->setLog($arrDetailPkey);

          $rsDetailKeyIndex = array_column($rsDetail,null,'pkey'); 
          //$this->setLog('$rsDetailKeyIndex');
          //$this->setLog($rsDetailKeyIndex);
          for($i=0;$i<count($arrDetailPkey);$i++) { 
                $detailkey = $arrDetailPkey[$i];  
                 // kalo sdh readonly, harga ambil ulang dr sistem
                
                 //$this->setLog('==> $rsDetailKeyIndex['.$detailkey.']['.$fieldRefKey.']');
                 if(!empty($rsDetailKeyIndex[$detailkey][$fieldRefKey])){  
                        foreach ($arrDataStructure as $key=>$data) { 

                            if ($data[0] == 'pkey' && !is_array($data[1]) &&  $data[1] == 'ref')
                                continue;
                            //$this->setLog('$detailkey '.$i.' => ' . $detailkey. ' = ' . $data[0] . '  -> ['. $detailkey . ']['.$key.']' .' **** ' . $rsDetailKeyIndex[$detailkey][$key]);
                            //$this->setLog(' $arrParam['.$data[0].'] = $rsDetailKeyIndex['.$detailkey.']['.$key.']' );
                            $arrParam[$data[0]][$i] = $rsDetailKeyIndex[$detailkey][$key];  
                        }
                 }   
            }

         for($i=0;$i<count($rsDetail);$i++) {   
            if(!in_array($rsDetail[$i]['pkey'], $arrDetailPkey)){  // Cek utk detailkey ada yg gk ad di table DB gk 
                // insert ulang 
                foreach ($arrDataStructure as $key=>$data) { 
                    if ($data[0] == 'pkey' && !is_array($data[1]) &&  $data[1] == 'ref')
                        continue;
                      
                    //$this->setLog(' $arrParam['.$data[0].'] = ' . $rsDetail[$i][$key] );
                    array_push($arrParam[$data[0]], $rsDetail[$i][$key]);  
                }
                
              // $this->setLog('====='); 
            } 
         }  
    }
     
    
    function getTableKeyAndObj($tableName, $returnValue = array()){ 
         
        if (defined('TABLENAME_SETTINGS') && isset(TABLENAME_SETTINGS[$tableName]) && !empty(TABLENAME_SETTINGS[$tableName])) { 
            $rs = TABLENAME_SETTINGS[$tableName];  
        }else{ 
            $sql = 'select * from '.$this->tablekey.' where tablename = ' .$this->oDbCon->paramString($tableName) ; 
            $rs  = $this->oDbCon->doQuery($sql); 

            // kalo gk ad auto add
            if (empty($rs)){
                $sql = 'insert into '.$this->tablekey.' (tablename) values (' .$this->oDbCon->paramString($tableName).')';
                $this->oDbCon->execute($sql); 

                $sql = 'select * from '.$this->tablekey.' where tablename = ' .$this->oDbCon->paramString($tableName) ; 
                $rs  = $this->oDbCon->doQuery($sql);  
            }
                
           $rs = $rs[0]; 
        }
        
        
        $arrReturn = array();
        
        if (empty($returnValue)){
            $arrReturn = array('key'=>$rs['pkey'],  
                               'obj'=>$this->getObjByTableName($tableName));
        }else{
            if(in_array('key',$returnValue)) $arrReturn['key'] = $rs['pkey'];
            if(in_array('obj',$returnValue)) $arrReturn['obj'] = $this->getObjByTableName($tableName);
            if(in_array('defaultstatus',$returnValue)) $arrReturn['defaultstatus'] = $rs['defaultstatus'];
            if(in_array('decimalnumber',$returnValue)) $arrReturn['decimalnumber'] = $rs['decimalnumber'];
        }
         
        return $arrReturn; 
        
    }
   
    function getTableNameAndObjById($id, $returnValue = array()){ 
         
        $sql = 'select * from '.$this->tablekey.' where pkey = ' .$this->oDbCon->paramString($id) ;  
        $rs  = $this->oDbCon->doQuery($sql); 
              
        
        if (empty($returnValue)){
            $arrReturn = array('tableName'=>$rs[0]['tablename'],  
                                'obj'=>$this->getObjByTableName($rs[0]['tablename'])); 
        }else{
            if(in_array('tableName',$returnValue)) $arrReturn['tableName'] = $rs[0]['tablename'];
            if(in_array('obj',$returnValue)) $arrReturn['obj'] = $this->getObjByTableName($rs[0]['tablename']); 
        }
        
        return $arrReturn; 
        
    }
  
    function getObjByTableName($tableName){ 
        return (isset($GLOBALS['ObjCol'][$tableName])) ? $GLOBALS['ObjCol'][$tableName] : null;
    }
    
    function formatWithColor($number){
          
        $number = $this->formatNumber($number,2);  
        $arr = explode(DECIMAL_SEPARATOR,$number);
        return $arr[0].'<span class="text-muted">.'.$arr[1].'</span>';
    }
    
    function swap(&$val1, &$val2){
        $temp = $val1;
        $val1 = $val2;
        $val2 = $temp;
    }
    
    function removeUnCheckRows(&$arrParam,$arrDataDetail,$chkPick='chkPick'){
        $arrWillBeRemoved = array();
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){  
            if ($arrParam[$chkPick][$i] == 0)
                array_push($arrWillBeRemoved, $i);
        }
        
        //$this->setLog(json_encode($arrParam['amount']));
        foreach($arrWillBeRemoved as $index){  
            foreach($arrDataDetail as $el){
             //$this->setLog(count($arrParam['amount']));
             if (isset($arrParam[$el[0]]) && is_array($arrParam[$el[0]]))  
               unset($arrParam[$el[0]][$index]); 
            }
         } 
                
        
        //$this->setLog(json_encode($arrParam['amount']));
        
        //reasign index
        foreach($arrDataDetail as $el){
           if (isset($arrParam[$el[0]]) && is_array($arrParam[$el[0]])) 
              $arrParam[$el[0]] = array_values($arrParam[$el[0]]);    
            //$this->setLog($el[0] .' ==> ' . json_encode($arrParam[$el[0]])); 
        }
        
    }
    
    
    function getPaymentMethodDetail($pkey,$paymentMethodKey=''){
        $paymentMethodKeyCriteria = '';
        if (!empty($paymentMethodKey))
            $paymentMethodKeyCriteria = ' and paymentkey = ' . $this->oDbCon->paramString($paymentMethodKey);

        $sql = 'select 
                    '.$this->tablePayment.'.* ,
                    '.$this->tablePaymentMethod.'.code as paymentmethodcode, 
                    '.$this->tablePaymentMethod.'.name as paymentmethodname 
                from  
                   '.$this->tablePayment.',
                   '.$this->tablePaymentMethod.' 
                where 
                   '.$this->tablePayment.'.paymentkey = '.$this->tablePaymentMethod.'.pkey and  
                    refkey in ('.$this->oDbCon->paramString($pkey,',').')
					'.$paymentMethodKeyCriteria.' order by paymentkey asc';	
        
        return $this->oDbCon->doQuery($sql); 
    } 
       
    
    function getPaymentVoucherDetail($pkey,$paymentMethodKey='', $recipientType = 1){
        
//        $fieldName = ($isSupplier) ?  $this->tableCashBank.'.supplierkey as vouchersupplierkey'  :  $this->tableCashBank.'.customerkey as vouchercustomerkey';
         
        switch($recipientType){
			 case 1:  
				 		$fieldName = $this->tableCashBank.'.customerkey as vouchercustomerkey'; 
				 		break;
			
            case 2 : 
				 		$fieldName = $this->tableCashBank.'.supplierkey as vouchersupplierkey'; 
				 		break;
				 
			 	 
			 case 3:  
				 		$fieldName = $this->tableCashBank.'.employeekey as voucheremployeekey'; 
				 		break; 
				 
		 }
        
        
        
        
        $paymentMethodKeyCriteria = '';
        if (!empty($paymentMethodKey))
            $paymentMethodKeyCriteria = ' and cashbankvoucherkey = ' . $this->oDbCon->paramString($paymentMethodKey);

        $sql = 'select 
                    '.$this->tablePayment.'.*,
                    '.$this->tableCashBank.'.code as vouchercode,
                    '.$this->tableCashBank.'.outstanding as voucheroutstanding,
                    '.$this->tableCashBank.'.amount as voucheramount,
                    '.$fieldName.',
                    '.$this->tableCashBank.'.statuskey as voucherstatuskey
                from  
                   '.$this->tablePayment.',
                   '.$this->tableCashBank.' 
                where 
                   '.$this->tablePayment.'.cashbankvoucherkey = '.$this->tableCashBank.'.pkey and  
                    '.$this->tablePayment.'.refkey = '.$this->oDbCon->paramString($pkey).$paymentMethodKeyCriteria.' order by cashbankvoucherkey asc';	
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        $totalRs = count($rs);
        for($i=0;$i<$totalRs;$i++)
            $rs[$i]['voucherlabel'] = $this->formatNumber($rs[$i]['voucheroutstanding']). ' ('.$rs[$i]['vouchercode'].')';
            
        return $rs;
    } 
       
    function getDownpaymentDetail($arrPkey,$downpaymentKey='', $forCustomer = true, $criteria = ''){
        $downpaymentKeyCriteria = '';
        if (!empty($downpaymentKey))
            $downpaymentKeyCriteria = ' and downpaymentkey = ' . $this->oDbCon->paramString($downpaymentKey);

        $recipient = ($forCustomer) ?  $this->tableDownpayment.'.customerkey as downpaymentcustomerkey' :  $this->tableDownpayment.'.supplierkey as downpaymentsupplierkey';
        
        $sql = 'select 
                    '.$this->tableDownpaymentDetail.'.*,
                    '.$this->tableDownpayment.'.code as refcode,
                    '.$this->tableDownpayment.'.trdate as downpaymentdate,
                    '.$recipient.',
                    '.$this->tableDownpayment.'.outstanding  as downpaymentoutstanding,
                    '.$this->tableDownpayment.'.rate as downpaymentrate,
                    '.$this->tableDownpayment.'.currencykey as downpaymentcurrencykey,
                    '.$this->tableDownpayment.'.statuskey as downpaymentstatuskey
                from  
                    '.$this->tableDownpaymentDetail.',
                    '.$this->tableDownpayment.'
                where 
                    '.$this->tableDownpaymentDetail.'.refkey in ('.$this->oDbCon->paramString($arrPkey,',').') '.$downpaymentKeyCriteria.' and
                    '.$this->tableDownpaymentDetail.'.downpaymentkey = '.$this->tableDownpayment.'.pkey
                order by downpaymentkey asc';	
         
        $sql .= ' ' . $criteria = '';
        
        return $this->oDbCon->doQuery($sql); 
    } 
  
     
    function getSystemWeight(){
       return  $this->getSystemUnit(SYSTEM_UNIT['weight']);   
    }
    
    function getSystemUnit($typekey = ''){
        
        $sql = 'select * from '.$this->tableUnit.' where 1=1 ';
        
        if (!empty($typekey))
            $sql .= ' and unittype = ' . $this->oDbCon->paramString($typekey);
        
        $sql .= ' order by orderlist asc';
        
        return $this->oDbCon->doQuery($sql); 
    }
    
    function getRefTableData($obj,$rsDetail,$refFieldName='',$detailCriteria= ''){
        
        $refFieldName = (empty($refFieldName)) ? 'reftabletype' : $refFieldName;
        
        $rsObj = array(); 
        
        //get distinct table 
        $arrDetail = array_column($rsDetail,$refFieldName);
        $arrDetail = array_unique($arrDetail);
         
        //for each tabletype
        
        foreach ($arrDetail as $row){
            $tabletypekey = $row;
             
            $arrRefKey = array();
            foreach($rsDetail as $detailRow)
                if($detailRow['reftabletype'] == $tabletypekey)
                array_push($arrRefKey,$detailRow['refkey']);
                
            $objType = $this->getTableNameAndObjById($tabletypekey); 
            $sql = 'select * from ' .$objType['tableName'].' where pkey in ('.implode(',',$arrRefKey).')';
            $rsTemp = $this->oDbCon->doQuery($sql);
            $rsTemp = array_column($rsTemp, null, 'pkey');
            $rsObj[$tabletypekey] = $rsTemp;
        }
        
        
        return $rsObj;
        
    }
    
    function getRefTableQuery($arrConcatField){
        $sql = 'select distinct(reftabletype)  as reftabletype from '.$this->tableName;  
        $rsTableType = $this->oDbCon->doQuery($sql);	
        $arrTableType = array_column($rsTableType,'reftabletype');

        $arrConcat = array(); 
        $arrJoin = array();
        foreach($arrTableType as $tableType){
           $rsObjType = $this->getTableNameAndObjById($tableType); 
           $refTableName = $rsObjType['tableName'];
            
           $sqlJoin = 'left join '.$refTableName.' on '.$this->tableName.'.refkey = '.$refTableName.'.pkey and reftabletype = ' . $this->oDbCon->paramString($tableType); 
           array_push($arrJoin, $sqlJoin);
            
           foreach($arrConcatField as $key=>&$row) {  
                array_push($row['value'], $refTableName.'.'.$row['field']);  
           }
        }
        unset($row);
         
        $arrConcatSql = array();
        foreach($arrConcatField as $key=>$row) {   
            array_push($arrConcatSql, 'CONCAT_WS(\'\','.implode(',',$row['value']).') as '.$key);    
        }
        
        $joinString = '';
        $concatString = '';
        if(!empty($arrTableType)){
             $joinString = ' ' .implode(' ', $arrJoin);
             $concatString =  ','.implode(',', $arrConcatSql);
        }
  
        return array('joinString' => $joinString, 'concatString' => $concatString);
    }
    
    function getMonthPeriod($startPeriod, $endPeriod){
            $startDt = date("Y-m-d", strtotime($startPeriod));
            $endDt = date("Y-m-d", strtotime($endPeriod)); 

            $start    = (new DateTime($startDt))->modify('first day of this month');
            $end      = (new DateTime($endDt))->modify('first day of next month');
            $interval = DateInterval::createFromDateString('1 month');
            $period   = new DatePeriod($start, $interval, $end);
        
            return $period;
    }
    
    function hasAllWarehouseAccess($employeekey = ''){ 
        
        $employeekey = (empty($employeekey)) ? $this->userkey : $employeekey;
     
        $sql = 'select allwarehouseaccess from ' . $this->tableEmployee .' where pkey = ' . $this->oDbCon->paramString($employeekey) ;  
        $rs = $this->oDbCon->doQuery($sql);
        
        return ($rs[0]['allwarehouseaccess'] == 1) ? true : false;
    }
    
     
    function hasAllCustomerAccess($employeekey=''){
        $employeekey = (empty($employeekey)) ? $this->userkey : $employeekey;
       
        $sql = 'select allcustomeraccess from ' . $this->tableEmployee .' where pkey = ' . $this->oDbCon->paramString($employeekey) ;  
        $rs = $this->oDbCon->doQuery($sql);
        
        return ($rs[0]['allcustomeraccess'] == 1) ? true : false; 
    }
       
    function hasAllSalesAccess($employeekey=''){
        $employeekey = (empty($employeekey)) ? $this->userkey : $employeekey;
       
        $sql = 'select allsalesaccess from ' . $this->tableEmployee .' where pkey = ' . $this->oDbCon->paramString($employeekey) ;  
        $rs = $this->oDbCon->doQuery($sql);
        
        return ($rs[0]['allsalesaccess'] == 1) ? true : false; 
    }
  
    function hasAllCOAAccess($employeekey = ''){ 
        
        $employeekey = (empty($employeekey)) ? $this->userkey : $employeekey;
     
        $sql = 'select allcoaaccess from ' . $this->tableEmployee .' where pkey = ' . $this->oDbCon->paramString($employeekey) ;  
        $rs = $this->oDbCon->doQuery($sql);
        
        return ($rs[0]['allcoaaccess'] == 1) ? true : false;
    }
    
    function generatePrintTableRow($opt){
        $docWidth = (!isset($opt['docWidth'])) ? $this->printDocWidth : $opt['docWidth'];
        $docWidth = str_replace('px','',$docWidth);
        
        $rowClass =  (isset($opt['class']) && !empty($opt['class'])) ? 'class="'.$opt['class'].'"' : '';
        $cellArr = $opt['cell'];
        
        $row = '<tr '.$rowClass.'>';
        
        $totalWidth = 0;
        foreach($cellArr as &$cell){ 
             if (isset($cell['width']) && !empty($cell['width'])) { 
                    $width = str_replace('px','',$cell['width']);
                    $totalWidth += $width;
             }
        } 
        unset($cell);
        
        $growWidth = $docWidth - $totalWidth;
        if ($growWidth < 0) $growWidth = 0;
            
        foreach($cellArr as $cell){ 
            
            $arrStyle = array();
            if (isset($cell['align']) && !empty($cell['align'])) 
                array_push($arrStyle , 'text-align: '. $cell['align'] .';');
             
            $width  = (isset($cell['width']) && !empty($cell['width'])) ? $cell['width'] : $growWidth; 
                
            array_push($arrStyle , 'width: '.  $width .'px;');
            
            
            if (isset($cell['style']) && !empty($cell['style'])) 
                array_push($arrStyle , $cell['style']);
            
            $style = (!empty($arrStyle)) ? 'style="'.implode(' ' , $arrStyle).'"' : '';
            $label = (isset($cell['label']) && !empty($cell['label'])) ? $cell['label'] : ''; 
            
            
            if (isset($cell['format']) && !empty($cell['format'])) { 
                switch($cell['format']) {
                        case 'integer' : $label = $this->formatNumber($label,0); 
                                         break;
                        case 'number' : $label = $this->formatNumber($label); 
                                        break;
                }
            }
            
            $row .= '<td '.$style.'>'.$label.'</td>';
        }
        
        $row .= '</tr>';
        
        //$this->setLog($row);
        return $row;
    }
    
    function convertMonthToYear($month){
        
        if ($month == 0)
            return ' 0 Bulan';
        
        $return = '';
        
        $year = floor($month/12);
        if($year != 0)    
            $return .= $year . ' Tahun ';
        
        $month = $month % 12;
        if($month > 0 )
            $return .= $month .' Bulan';
        
        
        return $return;
    }
    
    
    function getDateDifference($date_1 , $date_2 , $formatList = array()){
        return '';
        
        // harus test ulang %d nya bener gk
        // coba test 1 tahun lebih, %d balikinnya nilai 
        
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);

        $interval = date_diff($datetime1, $datetime2); 
        //%y Year %m Month %d Day %h Hours %i Minute %s Seconds
        $dateDiff = array();
        
        
        $arrLabel = array(); 
        $arrLabel['%y'] = $this->lang['year'];
        $arrLabel['%m'] = $this->lang['month'];
        $arrLabel['%d'] = $this->lang['day'];
        $arrLabel['%h'] = $this->lang['hour'];
        $arrLabel['%i'] = $this->lang['minute'];
        $arrLabel['%s'] = $this->lang['second'];
            
        if (empty($formatList))
            $formatList = array( '%y', '%m', '%d', '%h', '%i','%s');
        
        foreach($formatList as $format){
            $temp = $interval->format($format);
            if ($temp > 0 ) array_push($dateDiff, $temp . ' ' . $arrLabel[$format]);
        }
       
        return (empty($dateDiff)) ? '0 ' . $this->lang['second'] : implode (' ' ,$dateDiff);
       // return $interval->format($differenceFormat);

    }

    function updateTablekeyStatus(){
         
        if(empty($this->tableName) || empty($this->securityObject))
            return;
        
        try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 
 
            $sql = 'update tablekey set statuskey = 
                                    (select statuskey from '.$this->tableSecurityObject.', '.$this->tableUserSecurityObject.' where '.$this->tableSecurityObject.'.pkey = '.$this->tableUserSecurityObject.'.security_object_key and '.$this->tableSecurityObject.'.modulecode = ' . $this->oDbCon->paramString($this->securityObject).')
                    where tablename = '.$this->oDbCon->paramString($this->tableName).'  
                   '; 
            $this->oDbCon->execute($sql); 

            $this->oDbCon->endTrans();   
        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	 
         
    }
    
    function prepareMultiLevelDetail($arrParam,$details){
        $temp = 1;
        $arrTemp = $arrParam;
        $this->explodeParamDetail(1,$arrParam,$arrTemp, $details);
        
        return $arrParam;
    }
    
    function explodeParamDetail($level,&$arrParam,&$arrTemp, $details){ 
          
         if($level <= 1){
            // unset all details
            // so the array index dont has any conflict
            foreach($details as $itemDetails){
               foreach($itemDetails as $item){
                    // hidDetailKey harus di unset, karena ad nilai pkey ketika edit
                    if(in_array($item[0], $this->arrExcludeFieldOnMultiDetail))  continue;
                    unset($arrParam[$item[0]]);
                }  
            } 
            
        }

        // loop utk setiap dataset
        foreach($details as $itemDetails){ 
            
         $hidDetailsPkeyField = $itemDetails['pkey'][0].'TotalRows';
             
	     // jumlah total rows yg dikirm pasti urutannya sesuai dengan paramnya		
         $groupName = (isset($arrParam[$hidDetailsPkeyField][$level])) ? $hidDetailsPkeyField : 'hidTotalRows'; 

         if(!isset($arrParam[$groupName][$level])) continue;

         $hidTotalRowsPerLevel = $arrParam[$groupName][$level];
        
         for($i=0;$i<count($hidTotalRowsPerLevel);$i++){
                
                foreach($itemDetails as $key=>$item){ 
           
                        //$this->setLog('total rows ' . $level.' = ' . $hidTotalRowsPerLevel[$i]);
            
                        $totalRows = $hidTotalRowsPerLevel[$i];
                        for($j=0;$j<$totalRows;$j++){  
                
                        // hidDetailKey harus di unset, karena ad nilai pkey ketika edit
                        if(in_array($item[0], $this->arrExcludeFieldOnMultiDetail))  continue; // or $item[1] == ref .. 

                        // initiate array
                        if(!isset($arrParam[$item[0]][$i]))
                            $arrParam[$item[0]][$i] = array();

                            
                        //$arrPop = array_shift($arrTemp[$item[0]]); 
                        // php 8
                        if (!empty($arrTemp[$item[0]]) && is_array($arrTemp[$item[0]])) {
                            $arrPop = array_shift($arrTemp[$item[0]]);
                            array_push($arrParam[$item[0]][$i],$arrPop);
                        } else {
                            $arrPop = null; // or handle the "nothing to shift" case
                        }

                            
                            
                    }   
                } 
                
            } 
            
            
        }  
      
    } 
     
    function ucFirst($string){ 
        return ucfirst(strtolower($string));
    }
    
    function startTimeLog(){
        $this->setLog('Start');
        $_SESSION['START_TIME_LOG'] = microtime(TRUE);
    }
     
    function endTimeLog(){ 
        $end_time = microtime(TRUE); 
        $time_taken = $end_time - $_SESSION['START_TIME_LOG'] ; 
        $time_taken = round($time_taken,5); 
        $this->setLog('Generated in '.$time_taken.' seconds.');
    }
    
    function getPaymentDetailCollections($rs,$indexField, $criteria = ''){ 
         
        // penamaan table nanti buat bisa overwrite
        
        $sql = 'select 
                    '.$this->tablePayment.'.pkey,  
                    '.$this->tablePayment.'.refkey,  
                    '.$this->tablePayment.'.amount,
                    '.$this->tablePaymentMethod.'.pkey as paymentmethodkey,
                    '.$this->tablePaymentMethod.'.name as paymentmethodname
                from 
                    '.$this->tableName.'
                        left join '.$this->tablePayment.' on '.$this->tableName.'.pkey = '.$this->tablePayment.'.refkey
                        left join '.$this->tablePaymentMethod.' on  '.$this->tablePaymentMethod.'.pkey = '.$this->tablePayment.'.paymentkey
                where 
                    1=1 
                '; 
		
        $sql .= ' ' . $criteria;
        $rsAllDetail = $this->oDbCon->doQuery($sql);
        
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }
    
    
    function getDetailCollections($rs,$indexField, $criteria = ''){ 
        $rsAllDetail = $this->getDetailWithRelatedInformation(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }
    
    function reindexDetailCollections($rsAllDetail,$indexField){
        $rsDetailCol = array();
        $totalCount = count($rsAllDetail ?? []);
        
        for($i=0;$i<$totalCount;$i++){
            $indexKey = $rsAllDetail[$i][$indexField];  
            if(!isset($rsDetailCol[$indexKey])) $rsDetailCol[$indexKey] = array(); 
            array_push($rsDetailCol[$indexKey], $rsAllDetail[$i]);
        } 

        return $rsDetailCol;
    }
    
 
    function removeMultipleAndUnusedSpace($input){
        return trim(preg_replace('!\s+!', ' ', $input));
    }
    
    function trimData($arrParam){
          
        $arrPost = array_keys($arrParam);
        $arrUnsetKeys = array();
        foreach($this->arrData as $datakey=>$data){
            if (!in_array($data[0], $arrPost))
                array_push($arrUnsetKeys, $datakey);
        }

        foreach($arrUnsetKeys as $key)
            unset($this->arrData[$key]);
         
    }
    
    function getDetailForAPI($arrKey, $arrIndex = array()){ return array();  }
    
    function compileAPIField($rs, $fields, $showDetails = array(), $level = 0){ 
        
        // sekali query diawal
        // kalo 2 level masalah (lupa sudah solve blm)
        if(!empty($showDetails)){  
            
            $arrIndex = array();
            foreach($fields as $row){
                if(empty($row['detail'])) continue;
                
                
                // field difilter dulu sesuai dengan request API 
                 if ($showDetails[0] == 'all' || in_array($row['paramName'], $showDetails)){
                     array_push($arrIndex, $row['paramName']);   
                 }
                
            }
            
            $arrKey = (!empty($rs)) ? array_column($rs,'pkey') : array();
            $rsDetailsCol =  $this->getDetailForAPI($arrKey,$arrIndex);
            
            
                //$this->setLog('Baseclass >>>>>',true);
                //$this->setLog($arrKey,true);
                //$this->setLog($arrIndex,true);
                //$this->setLog($rsDetailsCol,true);
            
        }

        $returnValue = array();
		
		
        foreach($rs as $row){
            $arrDetail = array();
            $rowPkey = 0;
            
            foreach($fields as $key => $apiRow){
                 // kalo di rs gk ad, lewatin
                 
                 // kalo null ntah kenapa jd gk keluar
                 // kalo pake array_key_exists(), karena issset selalu balikin false kalo isinya null
                 /*if(!isset($row[$key]))  continue; */
                
                 $value = $row[$key];
                  
                 if($key == 'pkey') $rowPkey = $value;
				 if($key == 'refkey') $rowRefkey = $value;
				
                 $indexName = $apiRow['paramName'];   // tampung dulu sebelum berubah nilainya
                 
                $format = '';
                if(isset($apiRow['return'])){ 
                    $returnParam = $apiRow['return'];
                    if(isset($returnParam['isReturn']) && !$returnParam['isReturn']) continue;

                    $format = (isset($apiRow['return']['format'])) ? $apiRow['return'] : '';

                    // overwrite paramname
                    $value =  (isset($returnParam['paramName'])) ? $row[$returnParam['paramName']] :  $value;
                }
				
				$paramKey  = $rowPkey;
                
				// khusus path jenis file (dsb) dan sifatnya di  
				if(isset($format['format']) && in_array($format['format'],array('file')) && $level > 0) 
					$paramKey = $rowRefkey;
                
                $value = $this->formatAPIReturn($value,$format, $paramKey );
                $arrDetail[$indexName] = $value;
                 
            }
            
            if(!empty($showDetails)){ 
                //loop utk berapa jenis detail
                
                //$this->setLog('detail loop',true);
                //$this->setLog($rsDetailsCol,true);
                foreach($rsDetailsCol as $key => $detailRow){   
                    // biar tetep return empty array
                    // if(isset($detailRow[$rowPkey])){  
                        $arrDetail[$key] = $this->compileAPIField($detailRow[$rowPkey], $fields[$key]['detail'], $showDetails, $level + 1);  
                    // }
                }
            }
            
            array_push($returnValue, $arrDetail);
        } 
  
        return $returnValue;
    }
    
    /*function compileAPIField($rs, $fields, $showDetails = false){ 
        
        // sekali query diawal
        if($showDetails){  
            $arrKey = array_column($rs,'pkey');
            $rsDetailsCol = $this->getDetailForAPI($arrKey); 
        }

        $returnValue = array();
        foreach($rs as $row){
            $arrDetail = array();
            $rowPkey = 0;
            foreach($row as $key=>$field){   
                
                if(!isset($fields[$key])) continue;
                    
                if($key == 'pkey') $rowPkey = $field;
                $indexName = $fields[$key]['paramName'];   // tampung dulu sebelum berubah nilainya

                $format = '';
                if(isset($fields[$key]['return'])){ 
                    $returnParam = $fields[$key]['return'];
                    if(isset($returnParam['isReturn']) && !$returnParam['isReturn']) continue;

                    $format = (isset($fields[$key]['return']['format'])) ? $fields[$key]['return']['format'] : '';

                    // overwrite paramname
                    $field =  (isset($returnParam['paramName'])) ? $row[$returnParam['paramName']] :  $field;
                }

                $field = $this->formatAPIReturn($field,$format);
                $arrDetail[$indexName] = $field;
                 
            }

            
            if($showDetails){  
                //loop utk berapa jenis detail
                foreach($rsDetailsCol as $key => $detailRow){  
                    if(isset($detailRow[$rowPkey]))
                        $arrDetail[$key] = $this->compileAPIField($detailRow[$rowPkey], $fields[$key]['detail']); 
                }
            }
            
            array_push($returnValue, $arrDetail);
        } 
        
        //$this->setLog($returnValue,true);
        return $returnValue;
    }*/
    
    function formatAPIReturn($value,$arrFormat,$pkey){
        $format = (isset ($arrFormat['format'] ) ) ? $arrFormat['format'] : '';
 
        switch ($format){
            case 'mktime' : return strtotime($value);  
            case 'file' :  if (!empty($value)){
                                $uploadFolder = (isset($arrFormat['path']) && !empty($arrFormat['path'])) ? $arrFormat['path'] :$this->uploadFolder  ;
                                return ($this->useStorage) ? $this->createPresignedURL(DOMAIN_NAME.'/'.$uploadFolder.$pkey.'/'. $value )  : HTTP_HOST.'download/'.$arrFormat['path'].$pkey.'/'.$value  ;
                            }
                            return ''; 
                            //return (!empty($value)) ? HTTP_HOST.'download/'.$arrFormat['path'].$pkey.'/'.$value : '';   
        }
        
        return $value;
    }
    
    function useRealization(){
        return $this->loadSetting('useRealization');
    }
    
    function getFormListSetting($formName,$userkey = 0){
        $sql = 'select 
                    '.$this->tableSettingListHeader.'.pkey,  
                    '.$this->tableSettingListDetail.'.code, 
                    '.$this->tableSettingListDetail.'.title, 
                    '.$this->tableSettingListDetail.'.width 
                from 
                    '.$this->tableSettingListHeader.',
                    '.$this->tableSettingListDetail.'
                where
                    '.$this->tableSettingListHeader.'.name = '.$this->oDbCon->paramString($formName).' and
                    '.$this->tableSettingListHeader.'.pkey = '.$this->tableSettingListDetail.'.refkey and
                    '.$this->tableSettingListHeader.'.statuskey = 1 and
                    '.$this->tableSettingListHeader.'.userkey = '.$this->oDbCon->paramString($userkey);
         
        return $this->oDbCon->doQuery($sql);
                                          
    }
    
    function generateDataListColumn($formName){
       
        $rs = $this->getFormListSetting($formName,$this->userkey);
          
        // update dbfield sesuai dengan settingan class
        $availableColumn = $this->arrDataListAvailableColumn;
        $availableColumn = array_column($availableColumn,null,'code');
        for($i=0;$i<count($rs);$i++){
            // ambil dbfield dan setttingan lain sesuai settingan di class
            $rs[$i]['dbfield'] = $availableColumn[$rs[$i]['code']]['dbfield'];
           // $rs[$i]['function'] = (isset($availableColumn[$rs[$i]['code']]['function'])) ? $availableColumn[$rs[$i]['code']]['function'] : '';
            $rs[$i]['align'] = (isset($availableColumn[$rs[$i]['code']]['align'])) ? $availableColumn[$rs[$i]['code']]['align'] : '';
            $rs[$i]['format'] = (isset($availableColumn[$rs[$i]['code']]['format'])) ? $availableColumn[$rs[$i]['code']]['format'] : '';
        }
         
        
        $availableColumn = $this->arrDataListAvailableColumn;
        
        // ambil hanya yg default   
        $arrColumn = array(); 
        if(empty($rs)){   
              for($i=0;$i<count($availableColumn);$i++){
                if(!isset($availableColumn[$i]['default']) || !$availableColumn[$i]['default']) continue; 
                array_push($rs,$availableColumn[$i]);   
              } 
        } 
        
        foreach($rs as $row){

            $arrSetting = array();

            $arrSetting['code'] = $row['code'];
            $arrSetting['title'] = $row['title'];
            $arrSetting['dbfield'] = $row['dbfield'] ;  
            //$arrSetting['function'] = (isset($row['function'])) ? $row['function'] : '';   
            $arrSetting['width'] = (isset($row['width'])) ? $row['width'] : '';   
            $arrSetting['align'] = (isset($row['align'])) ? $row['align'] : '';   
            $arrSetting['format'] = (isset($row['format'])) ? $row['format'] : '';

            array_push($arrColumn,$arrSetting); 

        }

        
        
        // hapus arrColumn yg yg widthnya paling besar
        $maxWidth = 0;
        $maxTitle = '';
         for($i=0;$i<count($arrColumn); $i++){
            if (!isset($arrColumn[$i]['width']) || empty($arrColumn[$i]['width'])) $arrColumn[$i]['width'] = 100;
            
            $width = intval($arrColumn[$i]['width']);
            if($width > $maxWidth){
                $maxWidth = $width;
                $maxTitle = $arrColumn[$i]['title'];
            }
            
        }
         
        for($i=0;$i<count($arrColumn); $i++){
            if ($arrColumn[$i]['title'] == $maxTitle){
                $arrColumn[$i]['width'] = '';
                break;
            }
        }
        
        return $arrColumn;
    }
    
    function generateFormListColumn($formName){
        //$this->setLog($formName,true);
        
        $rs = $this->getFormListSetting($formName);
        
        $arrColumn = array();
        
        foreach($rs as $row){

            $arrSetting = array();
            array_push($arrSetting, ucwords($this->lang[$row['title']]));      
            array_push($arrSetting, $row['dbfield']);  

            if (!empty($row['width']))  array_push($arrSetting, $row['width']);   
            if (!empty($row['align']))  array_push($arrSetting, $row['align']);      
            if (!empty($row['format']))  array_push($arrSetting, $row['format']);      

            array_push($arrColumn,$arrSetting); 

        }
         
        return $arrColumn;
    }
    
    function getFilterItem($filterCriteria){
        
         $key = $filterCriteria['field']; 
         $tableName = (isset($filterCriteria['table'])) ? $filterCriteria['table'] : '';
        
         if(empty($filterCriteria['sql'])){
             switch ($key){
                    // gk bisa pindahin ke default, karena di baseclass blm ke define table statusnya
                    case 'statuskey': $sql = 'select pkey,status as name from '. $this->tableStatus ;
                                      break;  
                    case 'warehousekey':  
                                         $sql = 'select pkey,name from '. $this->tableWarehouse .' where 1=1 ' ; 
                                         $sql .=  $this->getWarehouseCriteria($this->tableWarehouse) ;
                                      break;  
                    // standart nya... 
                    default :  $sql = 'select pkey,name from '. $tableName .' where 1=1 ' ;  
                              break;  
             }
         }else{
             $sql = $filterCriteria['sql'];
         }
         
         
         $rs = $this->oDbCon->doQuery($sql); 
         return $rs;
        
    }
    
    function replaceNewLine($text,$replacement = '<br>'){
        return preg_replace( "/\r|\n/", $replacement, $text );
    }
    
    function isMobile(){ 

        $mobileDetect  = new Mobile_Detect();
        if($mobileDetect->isMobile())   
           return true;

        return false;


        /*$useragent=$_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
            return true;
        
        return false; */
    }
    
    function getDBSize($db='', $unit = '') { 
        
      if (empty($db)) 
          $db = $this->oDbCon->dbname;
              
      $sql = 'SELECT coalesce(SUM( data_length + index_length ),0) as totalsize FROM information_schema.tables WHERE table_schema = '.$this->oDbCon->paramString($db); 
      $rs = $this->oDbCon->doQuery($sql);
        
      $size = $rs[0]['totalsize'];
          
      return (empty($unit)) ? $size :  $this->convertSize($size,$unit);    
    }
    
    
    function getFolderSize($folder) {
        $totalSize = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder)) as $file) {
            if ($file->isFile()) {
                $totalSize += $file->getSize();
            }
        }

        return $totalSize; // size in bytes
    }

//    function getFolderSize($directory){ 
//        
//        $totalSize = 0;
//        $directoryArray = scandir($directory);
//
//        foreach($directoryArray as $key => $fileName){
//            if($fileName != ".." && $fileName != "."){
//                if(is_dir($directory . "/" . $fileName)){
//                    $totalSize = $totalSize + $this->getFolderSize($directory . "/" . $fileName);
//                } else if(is_file($directory . "/". $fileName)){
//                    $totalSize = $totalSize + filesize($directory. "/". $fileName);
//                }
//            }
//        }
//        
//      return  $totalSize;    
//    }
//    
    
    function convertSize($size,$unit = 'MB', $baseunit = 'byte'){
        
        
      $bytesize = 1024;       
      $baseunit =  strtolower($baseunit);  
      $unit =  strtolower($unit);   
      $arrInc = array('byte','kb','mb','gb'); 
             
      $pow = 0;
      foreach($arrInc as $row){    
         $pow = ($row == $baseunit) ? 0 : $pow + 1;  
         if ($row == $unit) break;  // end
      }
         
      $pow = ($pow == 0) ? 1 : pow($bytesize,$pow) ;
         
      $size = $size  / $pow;
             
      return $size;    
    }
    
    function getS3DiskUsage(){
        $bucket = STORAGE['bucket'];
        $prefix = DOMAIN_NAME.'/'; 
        $totalSize = 0;

        try {
                $params = [
                    'Bucket' => $bucket,
                    'Prefix' => $prefix,
                ];

                do {
                    $client = $this->initS3Client();
                    $result = $client->listObjectsV2($params);

                    if ($result['KeyCount'] > 0) {
                        foreach ($result['Contents'] as $object) {
                            $totalSize += $object['Size'];
                        }
                    }

                    // Check for more objects
                    $params['ContinuationToken'] = $result['NextContinuationToken'] ?? null;

                } while (!empty($params['ContinuationToken']));

        } catch (AwsException $e) {
            $this->setLog($e->getMessage(),true);
        }

//        $this->setLog($this->convertSize($totalSize,'mb'),true);    
        return $totalSize;

    }
    
    function getDiskUsage($db='',$saveInformation=true){
        $dbSize  = $this->getDBSize($db);  
        $uploadSize = $this->getFolderSize($this->defaultDocUploadPath);
        
        // gk bisa pake useStorage, karena gk ad validasi tablename
//        if($this->useStorage('S3'))
        
        // jd sementara manual if else nya
        $useS3Storage = (defined("STORAGE") && !empty(STORAGE) && STORAGE['name'] == 'S3') ? true : false;
        if($useS3Storage)
            $uploadSize += $this->getS3DiskUsage();
        
         
        $totalSize = $dbSize + $uploadSize;
        
        if ($saveInformation){
               try{  

                    if(!$this->oDbCon->startTrans())
                        throw new Exception($this->errorMsg[100]); 
               
                   
                    $mbDatabaseSize = $this->convertSize($dbSize,'mb'); 
                    $mbDiskUsageSize = $this->convertSize($uploadSize,'mb'); 

                    $sql = 'update '.$this->tablePlanType.' set useddatabaseusage = ' . $this->oDbCon->paramString($mbDatabaseSize).' , useddiskusage = ' . $this->oDbCon->paramString($mbDiskUsageSize);
                    $this->oDbCon->execute($sql);
                    $this->oDbCon->endTrans();  
 
                } catch(Exception $e){
                    $this->oDbCon->rollback();  
                }	 
        }
            
        $return = array(
            'dbsize' => $dbSize,
            'filesize' => $uploadSize,
            'totalsize' => $totalSize
        );
         
//        $this->setLog($return,true);
        return $return;
    }
    
//    function getDiskUsage($db='', $unit = 'MB',$saveInformation = true){ 
//        $dbSize  = $this->getDBSize($db);  
//        $mbDatabaseSize = $this->convertSize($dbSize,'mb'); 
//        $dbSize = $this->convertSize($dbSize,$unit); 
//        
//        $uploadSize = $this->getFolderSize($this->defaultDocUploadPath); 
//        $mbDiskUsageSize = $this->convertSize($uploadSize,'mb'); 
//         
//        $uploadSize = $this->convertSize($uploadSize,$unit); 
//        
//        $totalSize = $dbSize + $uploadSize;
//            
//        if ($saveInformation){
//               try{  
//
//                    if(!$this->oDbCon->startTrans())
//                        throw new Exception($this->errorMsg[100]); 
//               
//                    $sql = 'update '.$this->tablePlanType.' set useddatabaseusage = ' . $this->oDbCon->paramString($mbDatabaseSize).' , useddiskusage = ' . $this->oDbCon->paramString($mbDiskUsageSize);
//                    $this->oDbCon->execute($sql);
//                    $this->oDbCon->endTrans();  
// 
//                } catch(Exception $e){
//                    $this->oDbCon->rollback();  
//                }	 
//        }
//            
//        $return = array(
//            'dbsize' => $dbSize,
//            'filesize' => $uploadSize,
//            'totalsize' => $totalSize
//        );
//         
//        return $return;
//        
//    }
    
    function updateColumnHeader($fileName,$arrParam){
        
        if (!isset($this->arrDataListAvailableColumn) || empty($this->arrDataListAvailableColumn)) return;
        
         try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 
 
            $rs = $this->getFormListSetting($fileName,$this->userkey);

            if(!empty($rs)){
                $sql = 'delete from '.$this->tableSettingListHeader.' where pkey = '. $this->oDbCon->paramString($rs[0]['pkey']);
                $this->oDbCon->execute($sql);

                $sql = 'delete from '.$this->tableSettingListDetail.' where refkey = '. $this->oDbCon->paramString($rs[0]['pkey']);
                $this->oDbCon->execute($sql);
            }

            $newPkey = $this->getNextKey($this->tableSettingListHeader);
            $sql = 'insert into '.$this->tableSettingListHeader.' (pkey,name,userkey,statuskey) values ('.$this->oDbCon->paramString($newPkey).','.$this->oDbCon->paramString($fileName).','.$this->oDbCon->paramString($this->userkey).',1)';
             
            $this->oDbCon->execute($sql);
  
            $arrAvailableCol = array_column($this->arrDataListAvailableColumn,null,'code');
           
             
            foreach($arrParam as $row){
                
                // jika kode dan dbfield tidak terdaftar
                if (!isset($arrAvailableCol[$row['code']])) continue; 
                
                $arrCol = $arrAvailableCol[$row['code']];
                
                $code = $row['code'];
                $width = str_replace('px','',$row['width']);
                $width = intval($width);
                
                $title = $arrCol['title']; 
                
                $sql = 'insert into '.$this->tableSettingListDetail.' (
                            code,
                            refkey,
                            title, 
                            width 
                        ) values (
                            '.$this->oDbCon->paramString($code).',
                            '.$this->oDbCon->paramString($newPkey).',
                            '.$this->oDbCon->paramString($title).', 
                            '.$this->oDbCon->paramString($width).' 
                        )'; 
                $this->oDbCon->execute($sql); 
            }
             
            $this->oDbCon->endTrans();  
 
        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	 
        
        
    }
    
    function overwriteConfig(){
            
        $className = get_class($this);
        
        $path = $className.'.config.php';
        $docPersonalizedFile = PERSONALIZED_DOC_PATH.$path;   
        
        if (is_file($docPersonalizedFile)) 
            require_once $docPersonalizedFile;
        
    }
    
    function setDefaultColumnHeader($arrDefaultColumn){ 
        
       // sekalian reorder sesuai dengan urutan config
       
       $newDataList = array();
       foreach($arrDefaultColumn as $configRow){ 
           foreach($this->arrDataListAvailableColumn as $key => $row){ 
                if($configRow == $row['code']){ 
                    $row['default'] = true;
                    array_push($newDataList, $row);
                    unset($this->arrDataListAvailableColumn[$key]);
                    break;
                }
           }
       }    
     
        
       // add yg tersisa
         foreach($this->arrDataListAvailableColumn as $key => $row){
            $row['default'] = false;
            array_push($newDataList, $row); 
         }
        
        $this->arrDataListAvailableColumn = $newDataList; 
         
         
    }
    
    function sortMultiDimensionArray($arr,$sortBy){
        // untuk sort array berdasarkan index 
        //$arr = array_column($arr,null,$sortBy);
        
        
        // gk bisa pake array_column karena ad kemungkinan title / index ny sama
        $arrKeys = array();
        foreach($arr as $row)
            array_push($arrKeys,$row[$sortBy]); 
        sort($arrKeys); 
         
        //harus unset yg sudah disort, agar gk masalah kalo punya title / index yg sama 
        $newArray = array(); 
        foreach($arrKeys as $row){  
            
            $temp = array();
            foreach($arr as $key => $arrRow){  
                if($arrRow[$sortBy] == $row){  
                    array_push($newArray, $arrRow);
                    unset($arr[$key]);
                    break;
                }
            }  
            
        }
         
        return $newArray;
        
    }
    
    function generateTransactionLogReport($criteria='',$order=''){
         $sql = 'select 
                    ' . $this->transactionLog .'.*,
                    ' . $this->tableEmployee .'.name as employeename,
                    ' . $this->transactionLogAction .'.name as actionname,
                    ' . $this->tablekey .'.tablename
                from 
                    ' . $this->transactionLog .',
                    ' . $this->tableEmployee .',
                    ' . $this->transactionLogAction .', 
                    ' . $this->tablekey .'

                where 
                    ' . $this->transactionLog .'.actionkey = ' . $this->transactionLogAction .'.pkey and
                    ' . $this->transactionLog .'.createdby = ' . $this->tableEmployee .'.pkey and
                    ' . $this->transactionLog .'.tablekey = ' . $this->tablekey .'.pkey ' ;

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs; 
    }
 
    function getObjMapping($tableName = '', $tablekey =''){ 
        
        if(!empty($tablekey)){ 
            $sql = 'select tablename from tablekey where tablekey.pkey = ' . $this->oDbCon->paramString($tablekey); 
            $table = $this->oDbCon->doQuery($sql); 
            $tableName = $table[0]['tablename'];
        }
 
        // perlu diduluin
        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayment.class.php';   
        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php';
        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Downpayment.class.php';
        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderInvoiceReceipt.class.php';

		switch ($tableName){  
         case 'ap': includeClass('AP.class.php'); return createObjAndAddToCol(new AP()); break;case 'ap_commission': includeClass('APCommission.class.php'); return createObjAndAddToCol(new APCommission()); break;case 'ap_commission_payment_header': includeClass('APCommissionPayment.class.php'); return createObjAndAddToCol(new APCommissionPayment()); break;case 'ap_customer_commission': includeClass('APCustomerCommission.class.php'); return createObjAndAddToCol(new APCustomerCommission()); break;case 'ap_customer_commission_payment_header': includeClass('APCustomerCommissionPayment.class.php'); return createObjAndAddToCol(new APCustomerCommissionPayment()); break;case 'ap_employee': includeClass('APEmployee.class.php'); return createObjAndAddToCol(new APEmployee()); break;case 'ap_employee_commission': includeClass('APEmployeeCommission.class.php'); return createObjAndAddToCol(new APEmployeeCommission()); break;case 'ap_employee_commission_payment_header': includeClass('APEmployeeCommissionPayment.class.php'); return createObjAndAddToCol(new APEmployeeCommissionPayment()); break;case 'ap_employee_payment_header': includeClass('APEmployeePayment.class.php'); return createObjAndAddToCol(new APEmployeePayment()); break;case 'api_log': includeClass('APILog.class.php'); return createObjAndAddToCol(new APILog()); break;case 'api_scheduler': includeClass('APIScheduler.class.php'); return createObjAndAddToCol(new APIScheduler()); break;case 'ap_payable_23': includeClass('APPayableTax23.class.php'); return createObjAndAddToCol(new APPayableTax23()); break;case 'ap_payable_23_payment_header': includeClass('APPayableTax23Payment.class.php'); return createObjAndAddToCol(new APPayableTax23Payment()); break;case 'ap_payment_header': includeClass('APPayment.class.php'); return createObjAndAddToCol(new APPayment()); break;case 'ar': includeClass('AR.class.php'); return createObjAndAddToCol(new AR()); break;case 'arap_employee_netting_header': includeClass('ARAPEmployeeNetting.class.php'); return createObjAndAddToCol(new ARAPEmployeeNetting()); break;case 'arap_netting_header': includeClass('ARAPNetting.class.php'); return createObjAndAddToCol(new ARAPNetting()); break;case 'ar_discount_approval_header': includeClass('ARDiscountApproval.class.php'); return createObjAndAddToCol(new ARDiscountApproval()); break;case 'ar_employee': includeClass('AREmployee.class.php'); return createObjAndAddToCol(new AREmployee()); break;case 'ar_employee_payment_header': includeClass('AREmployeePayment.class.php'); return createObjAndAddToCol(new AREmployeePayment()); break;case 'ar_payment_header': includeClass('ARPayment.class.php'); return createObjAndAddToCol(new ARPayment()); break;case 'ar_prepaid_23': includeClass('ARPrepaidTax23.class.php'); return createObjAndAddToCol(new ARPrepaidTax23()); break;case 'ar_prepaid_23_payment_header': includeClass('ARPrepaidTax23Payment.class.php'); return createObjAndAddToCol(new ARPrepaidTax23Payment()); break;case 'activity_log': includeClass('ActivityLog.class.php'); return createObjAndAddToCol(new ActivityLog()); break;case 'article': includeClass('Article.class.php'); return createObjAndAddToCol(new Article()); break;case 'article_category': includeClass('ArticleCategory.class.php'); return createObjAndAddToCol(new ArticleCategory()); break;case 'assembly_header': includeClass('Assembly.class.php'); return createObjAndAddToCol(new Assembly()); break;case 'asset': includeClass('Asset.class.php'); return createObjAndAddToCol(new Asset()); break;case 'asset_category': includeClass('AssetCategory.class.php'); return createObjAndAddToCol(new AssetCategory()); break;case 'asset_depreciation_header': includeClass('AssetDepreciation.class.php'); return createObjAndAddToCol(new AssetDepreciation()); break;case 'asset_group': includeClass('AssetGroup.class.php'); return createObjAndAddToCol(new AssetGroup()); break;case 'asset_purchase_header': includeClass('AssetPurchase.class.php'); return createObjAndAddToCol(new AssetPurchase()); break;case 'asset_transfer_header': includeClass('AssetTransfer.class.php'); return createObjAndAddToCol(new AssetTransfer()); break;case '_code': includeClass('AutoCode.class.php'); return createObjAndAddToCol(new AutoCode()); break;case 'banner': includeClass('Banner.class.php'); return createObjAndAddToCol(new Banner()); break;case 'bill_of_materials_header': includeClass('BillOfMaterials.class.php'); return createObjAndAddToCol(new BillOfMaterials()); break;case 'brand': includeClass('Brand.class.php'); return createObjAndAddToCol(new Brand()); break;case 'business_category': includeClass('BusinessCategory.class.php'); return createObjAndAddToCol(new BusinessCategory()); break;case 'business_category_suggestion': includeClass('BusinessCategorySuggestion.class.php'); return createObjAndAddToCol(new BusinessCategorySuggestion()); break;case 'coa_link': includeClass('COALink.class.php'); return createObjAndAddToCol(new COALink()); break;case 'campaign': includeClass('Campaign.class.php'); return createObjAndAddToCol(new Campaign()); break;case 'campaign_newsletter': includeClass('CampaignNewsletter.class.php'); return createObjAndAddToCol(new CampaignNewsletter()); break;case 'cancel_reason': includeClass('CancelReason.class.php'); return createObjAndAddToCol(new CancelReason()); break;case 'car': includeClass('Car.class.php'); return createObjAndAddToCol(new Car()); break;case 'car_category': includeClass('CarCategory.class.php'); return createObjAndAddToCol(new CarCategory()); break;case 'car_checklist': includeClass('CarChecklist.class.php'); return createObjAndAddToCol(new CarChecklist()); break;case 'car_maintenance_checklist_header': includeClass('CarMaintenanceChecklist.class.php'); return createObjAndAddToCol(new CarMaintenanceChecklist()); break;case 'car_revenue': includeClass('CarRevenue.class.php'); return createObjAndAddToCol(new CarRevenue()); break;case 'car_series': includeClass('CarSeries.class.php'); return createObjAndAddToCol(new CarSeries()); break;case 'car_service_maintenance_header': includeClass('CarServiceMaintenance.class.php'); return createObjAndAddToCol(new CarServiceMaintenance()); break;case 'car_turnover': includeClass('CarTurnover.class.php'); return createObjAndAddToCol(new CarTurnover()); break;case 'career_category': includeClass('CareerCategory.class.php'); return createObjAndAddToCol(new CareerCategory()); break;case 'career_field': includeClass('CareerField.class.php'); return createObjAndAddToCol(new CareerField()); break;case 'cash_advance': includeClass('CashAdvance.class.php'); return createObjAndAddToCol(new CashAdvance()); break;case 'cash_advance_realization_header': includeClass('CashAdvanceRealization.class.php'); return createObjAndAddToCol(new CashAdvanceRealization()); break;case 'cash_bank': includeClass('CashBank.class.php'); return createObjAndAddToCol(new CashBank()); break;case 'cash_bank_in_header': includeClass('CashBankIn.class.php'); return createObjAndAddToCol(new CashBankIn()); break;case 'cash_bank_out_header': includeClass('CashBankOut.class.php'); return createObjAndAddToCol(new CashBankOut()); break;case 'cash_bank_realization_header': includeClass('CashBankRealization.class.php'); return createObjAndAddToCol(new CashBankRealization()); break;case 'cash_bank_transfer_header': includeClass('CashBankTransfer.class.php'); return createObjAndAddToCol(new CashBankTransfer()); break;case 'cash_in_header': includeClass('CashIn.class.php'); return createObjAndAddToCol(new CashIn()); break;case 'cash_movement': includeClass('CashMovement.class.php'); return createObjAndAddToCol(new CashMovement()); break;case 'cash_out_header': includeClass('CashOut.class.php'); return createObjAndAddToCol(new CashOut()); break;case 'item_category': includeClass('Category.class.php'); return createObjAndAddToCol(new Category()); break;case 'change_item_sn_header': includeClass('ChangeItemSN.class.php'); return createObjAndAddToCol(new ChangeItemSN()); break;case 'chart_of_account': includeClass('ChartOfAccount.class.php'); return createObjAndAddToCol(new ChartOfAccount()); break;case 'chassis': includeClass('Chassis.class.php'); return createObjAndAddToCol(new Chassis()); break;case 'chassis_category': includeClass('ChassisCategory.class.php'); return createObjAndAddToCol(new ChassisCategory()); break;case 'city': includeClass('City.class.php'); return createObjAndAddToCol(new City()); break;case 'city_category': includeClass('CityCategory.class.php'); return createObjAndAddToCol(new CityCategory()); break;case 'company': includeClass('Company.class.php'); return createObjAndAddToCol(new Company()); break;case 'consignee': includeClass('Consignee.class.php'); return createObjAndAddToCol(new Consignee()); break;case 'contact_us': includeClass('Contact.class.php'); return createObjAndAddToCol(new Contact()); break;case 'contact_category': includeClass('ContactCategory.class.php'); return createObjAndAddToCol(new ContactCategory()); break;case 'container': includeClass('Container.class.php'); return createObjAndAddToCol(new Container()); break;case 'contract_duration': includeClass('ContractDuration.class.php'); return createObjAndAddToCol(new ContractDuration()); break;case 'cost_cash_out': includeClass('CostCashOut.class.php'); return createObjAndAddToCol(new CostCashOut()); break;case 'cost_rate_header': includeClass('CostRate.class.php'); return createObjAndAddToCol(new CostRate()); break;case 'cost_reconsile_header': includeClass('CostReconsile.class.php'); return createObjAndAddToCol(new CostReconsile()); break;case 'country': includeClass('Country.class.php'); return createObjAndAddToCol(new Country()); break;case 'course_header': includeClass('Course.class.php'); return createObjAndAddToCol(new Course()); break;case 'course_category': includeClass('CourseCategory.class.php'); return createObjAndAddToCol(new CourseCategory()); break;case 'credit_note_header': includeClass('CreditNote.class.php'); return createObjAndAddToCol(new CreditNote()); break;case 'currency': includeClass('Currency.class.php'); return createObjAndAddToCol(new Currency()); break;case 'currency_rate_header': includeClass('CurrencyRate.class.php'); return createObjAndAddToCol(new CurrencyRate()); break;case 'custom_code': includeClass('CustomCode.class.php'); return createObjAndAddToCol(new CustomCode()); break;case 'customer': includeClass('Customer.class.php'); return createObjAndAddToCol(new Customer()); break;case 'customer_category': includeClass('CustomerCategory.class.php'); return createObjAndAddToCol(new CustomerCategory()); break;case 'customer_downpayment': includeClass('CustomerDownpayment.class.php'); return createObjAndAddToCol(new CustomerDownpayment()); break;case 'customer_downpayment_settlement_header': includeClass('CustomerDownpaymentSettlement.class.php'); return createObjAndAddToCol(new CustomerDownpaymentSettlement()); break;case 'customer_features': includeClass('CustomerFeatures.class.php'); return createObjAndAddToCol(new CustomerFeatures()); break;case 'customer_insurance_policy': includeClass('CustomerInsurancePolicy.class.php'); return createObjAndAddToCol(new CustomerInsurancePolicy()); break;case 'customer_membership': includeClass('CustomerMembership.class.php'); return createObjAndAddToCol(new CustomerMembership()); break;case 'depot': includeClass('Depot.class.php'); return createObjAndAddToCol(new Depot()); break;case 'diagnose': includeClass('Diagnose.class.php'); return createObjAndAddToCol(new Diagnose()); break;case 'discount_scheme_header': includeClass('DiscountScheme.class.php'); return createObjAndAddToCol(new DiscountScheme()); break;case 'disposal_contract': includeClass('DisposalContract.class.php'); return createObjAndAddToCol(new DisposalContract()); break;case 'disposal_job_order_header': includeClass('DisposalJobOrder.class.php'); return createObjAndAddToCol(new DisposalJobOrder()); break;case 'disposal_purchase_order_header': includeClass('DisposalPurchaseOrder.class.php'); return createObjAndAddToCol(new DisposalPurchaseOrder()); break;case 'disposal_sales_invoice_header': includeClass('DisposalSalesInvoice.class.php'); return createObjAndAddToCol(new DisposalSalesInvoice()); break;case 'disposal_work_order_header': includeClass('DisposalWorkOrder.class.php'); return createObjAndAddToCol(new DisposalWorkOrder()); break;case 'disposal_work_order_dispatcher_header': includeClass('DisposalWorkOrderDispatcher.class.php'); return createObjAndAddToCol(new DisposalWorkOrderDispatcher()); break;case 'division': includeClass('Division.class.php'); return createObjAndAddToCol(new Division()); break;case 'download': includeClass('Download.class.php'); return createObjAndAddToCol(new Download()); break;case 'download_category': includeClass('DownloadCategory.class.php'); return createObjAndAddToCol(new DownloadCategory()); break;case 'downpayment': includeClass('Downpayment.class.php'); return createObjAndAddToCol(new Downpayment()); break;case 'emkl_commission_header': includeClass('EMKLCommission.class.php'); return createObjAndAddToCol(new EMKLCommission()); break;case 'emkl_hbl': includeClass('EMKLHouseBL.class.php'); return createObjAndAddToCol(new EMKLHouseBL()); break;case 'emkl_invoice_receipt_header': includeClass('EMKLInvoiceReceipt.class.php'); return createObjAndAddToCol(new EMKLInvoiceReceipt()); break;case 'emkl_job_order_header': includeClass('EMKLJobOrder.class.php'); return createObjAndAddToCol(new EMKLJobOrder()); break;case 'emkl_order_header': includeClass('EMKLJobOrderHeader.class.php'); return createObjAndAddToCol(new EMKLJobOrderHeader()); break;case 'emkl_order_invoice_header': includeClass('EMKLOrderInvoice.class.php'); return createObjAndAddToCol(new EMKLOrderInvoice()); break;case 'emkl_purchase_order_header': includeClass('EMKLPurchaseOrder.class.php'); return createObjAndAddToCol(new EMKLPurchaseOrder()); break;case 'emkl_reminder_job_order_header': includeClass('EMKLReminderJobOrder.class.php'); return createObjAndAddToCol(new EMKLReminderJobOrder()); break;case 'email_blast': includeClass('EmailBlast.class.php'); return createObjAndAddToCol(new EmailBlast()); break;case 'employee': includeClass('Employee.class.php'); return createObjAndAddToCol(new Employee()); break;case 'employee_category': includeClass('EmployeeCategory.class.php'); return createObjAndAddToCol(new EmployeeCategory()); break;case 'event': includeClass('Event.class.php'); return createObjAndAddToCol(new Event()); break;case 'faq_header': includeClass('FAQ.class.php'); return createObjAndAddToCol(new FAQ()); break;case 'filter_category': includeClass('FilterCategory.class.php'); return createObjAndAddToCol(new FilterCategory()); break;case 'gallery_header': includeClass('Gallery.class.php'); return createObjAndAddToCol(new Gallery()); break;case 'gallery_category': includeClass('GalleryCategory.class.php'); return createObjAndAddToCol(new GalleryCategory()); break;case 'general_journal_header': includeClass('GeneralJournal.class.php'); return createObjAndAddToCol(new GeneralJournal()); break;case 'genset': includeClass('Genset.class.php'); return createObjAndAddToCol(new Genset()); break;case 'give_opportunity': includeClass('GiveOpportunity.class.php'); return createObjAndAddToCol(new GiveOpportunity()); break;case 'hospital_cost_cash_out_header': includeClass('HospitalCostCashOut.class.php'); return createObjAndAddToCol(new HospitalCostCashOut()); break;case 'hospital_job_order_header': includeClass('HospitalJobOrder.class.php'); return createObjAndAddToCol(new HospitalJobOrder()); break;case 'hospital_selling_rate_header': includeClass('HospitalSellingRate.class.php'); return createObjAndAddToCol(new HospitalSellingRate()); break;case 'hospital_service_order_invoice_header': includeClass('HospitalServiceOrderInvoice.class.php'); return createObjAndAddToCol(new HospitalServiceOrderInvoice()); break;case 'hospital_work_order': includeClass('HospitalWorkOrder.class.php'); return createObjAndAddToCol(new HospitalWorkOrder()); break;case 'ilc_member': includeClass('ILCMember.class.php'); return createObjAndAddToCol(new ILCMember()); break;case 'installation_bast': includeClass('InstallationBAST.class.php'); return createObjAndAddToCol(new InstallationBAST()); break;case 'installation_work_order_header': includeClass('InstallationWorkOrder.class.php'); return createObjAndAddToCol(new InstallationWorkOrder()); break;case 'invoice_order_subscription_header': includeClass('InvoiceOrderSubscription.class.php'); return createObjAndAddToCol(new InvoiceOrderSubscription()); break;case 'invoice_period': includeClass('InvoicePeriod.class.php'); return createObjAndAddToCol(new InvoicePeriod()); break;case 'invoice_tax': includeClass('InvoiceTax.class.php'); return createObjAndAddToCol(new InvoiceTax()); break;case 'issue_category': includeClass('IssueCategory.class.php'); return createObjAndAddToCol(new IssueCategory()); break;case 'item': includeClass('Item.class.php'); return createObjAndAddToCol(new Item()); break;case 'item_adjustment_header': includeClass('ItemAdjustment.class.php'); return createObjAndAddToCol(new ItemAdjustment()); break;case 'item_category': includeClass('ItemCategory.class.php'); return createObjAndAddToCol(new ItemCategory()); break;case 'item_checklist': includeClass('ItemChecklist.class.php'); return createObjAndAddToCol(new ItemChecklist()); break;case 'item_checklist_group_header': includeClass('ItemChecklistGroup.class.php'); return createObjAndAddToCol(new ItemChecklistGroup()); break;case 'item_condition': includeClass('ItemCondition.class.php'); return createObjAndAddToCol(new ItemCondition()); break;case 'item_conversion': includeClass('ItemConversion.class.php'); return createObjAndAddToCol(new ItemConversion()); break;case 'item': includeClass('ItemDepot.class.php'); return createObjAndAddToCol(new ItemDepot()); break;case 'item_depot_movement': includeClass('ItemDepotMovement.class.php'); return createObjAndAddToCol(new ItemDepotMovement()); break;case 'filter_header': includeClass('ItemFilter.class.php'); return createObjAndAddToCol(new ItemFilter()); break;case 'item_in_header': includeClass('ItemIn.class.php'); return createObjAndAddToCol(new ItemIn()); break;case 'item_in_depot_header': includeClass('ItemInDepot.class.php'); return createObjAndAddToCol(new ItemInDepot()); break;case 'item_in_receive_header': includeClass('ItemInReceive.class.php'); return createObjAndAddToCol(new ItemInReceive()); break;case 'item_movement': includeClass('ItemMovement.class.php'); return createObjAndAddToCol(new ItemMovement()); break;case 'item_movement_po': includeClass('ItemMovementPO.class.php'); return createObjAndAddToCol(new ItemMovementPO()); break;case 'item_out_header': includeClass('ItemOut.class.php'); return createObjAndAddToCol(new ItemOut()); break;case 'item_out_delivery_header': includeClass('ItemOutDelivery.class.php'); return createObjAndAddToCol(new ItemOutDelivery()); break;case 'item_out_depot_header': includeClass('ItemOutDepot.class.php'); return createObjAndAddToCol(new ItemOutDepot()); break;case 'item': includeClass('ItemPackage.class.php'); return createObjAndAddToCol(new ItemPackage()); break;case 'item_promo_header': includeClass('ItemPromo.class.php'); return createObjAndAddToCol(new ItemPromo()); break;case 'item_specification': includeClass('ItemSpecification.class.php'); return createObjAndAddToCol(new ItemSpecification()); break;case 'item_unit': includeClass('ItemUnit.class.php'); return createObjAndAddToCol(new ItemUnit()); break;case 'item_upload_receipt_header': includeClass('ItemUploadReceipt.class.php'); return createObjAndAddToCol(new ItemUploadReceipt()); break;case 'job_details': includeClass('JobDetails.class.php'); return createObjAndAddToCol(new JobDetails()); break;case 'job_opportunities': includeClass('JobOpportunities.class.php'); return createObjAndAddToCol(new JobOpportunities()); break;case 'job_position': includeClass('JobPosition.class.php'); return createObjAndAddToCol(new JobPosition()); break;case 'journal_balancing': includeClass('JournalBalancing.class.php'); return createObjAndAddToCol(new JournalBalancing()); break;case 'lang': includeClass('Lang.class.php'); return createObjAndAddToCol(new Lang()); break;case 'location': includeClass('Location.class.php'); return createObjAndAddToCol(new Location()); break;case 'login_log': includeClass('LoginLog.class.php'); return createObjAndAddToCol(new LoginLog()); break;case 'logistic_sales_order_header': includeClass('LogisticSalesOrder.class.php'); return createObjAndAddToCol(new LogisticSalesOrder()); break;case 'logistic_sales_order_manifest_header': includeClass('LogisticSalesOrderManifest.class.php'); return createObjAndAddToCol(new LogisticSalesOrderManifest()); break;case 'management_team': includeClass('ManagementTeam.class.php'); return createObjAndAddToCol(new ManagementTeam()); break;case 'marketplace': includeClass('Marketplace.class.php'); return createObjAndAddToCol(new Marketplace()); break;case 'media': includeClass('Media.class.php'); return createObjAndAddToCol(new Media()); break;case 'medical_job_order_header': includeClass('MedicalJobOrder.class.php'); return createObjAndAddToCol(new MedicalJobOrder()); break;case 'medical_purchase_order_header': includeClass('MedicalPurchaseOrder.class.php'); return createObjAndAddToCol(new MedicalPurchaseOrder()); break;case 'medical_record_header': includeClass('MedicalRecord.class.php'); return createObjAndAddToCol(new MedicalRecord()); break;case 'medical_request_claim_header': includeClass('MedicalRequestClaim.class.php'); return createObjAndAddToCol(new MedicalRequestClaim()); break;case 'medical_sales_invoice_header': includeClass('MedicalSalesInvoice.class.php'); return createObjAndAddToCol(new MedicalSalesInvoice()); break;case 'medical_sales_order_quotation_header': includeClass('MedicalSalesOrderQuotation.class.php'); return createObjAndAddToCol(new MedicalSalesOrderQuotation()); break;case 'meeting_point': includeClass('MeetingPoint.class.php'); return createObjAndAddToCol(new MeetingPoint()); break;case 'meeting_point_suggestion': includeClass('MeetingPointSuggestion.class.php'); return createObjAndAddToCol(new MeetingPointSuggestion()); break;case 'meeting_schedule_header': includeClass('MeetingSchedule.class.php'); return createObjAndAddToCol(new MeetingSchedule()); break;case 'membership': includeClass('Membership.class.php'); return createObjAndAddToCol(new Membership()); break;case 'membership_attendance': includeClass('MembershipAttendance.class.php'); return createObjAndAddToCol(new MembershipAttendance()); break;case 'membership_level': includeClass('MembershipLevel.class.php'); return createObjAndAddToCol(new MembershipLevel()); break;case 'membership_subscription': includeClass('MembershipSubscription.class.php'); return createObjAndAddToCol(new MembershipSubscription()); break;case 'news': includeClass('News.class.php'); return createObjAndAddToCol(new News()); break;case 'news_category': includeClass('NewsCategory.class.php'); return createObjAndAddToCol(new NewsCategory()); break;case 'subscribe': includeClass('NewsletterSubscription.class.php'); return createObjAndAddToCol(new NewsletterSubscription()); break;case 'offer_simulator_header': includeClass('OfferSimulator.class.php'); return createObjAndAddToCol(new OfferSimulator()); break;case 'oil_type': includeClass('OilType.class.php'); return createObjAndAddToCol(new OilType()); break;case 'online_channel': includeClass('OnlineChannel.class.php'); return createObjAndAddToCol(new OnlineChannel()); break;case 'page': includeClass('Page.class.php'); return createObjAndAddToCol(new Page()); break;case 'page_category': includeClass('PageCategory.class.php'); return createObjAndAddToCol(new PageCategory()); break;case 'partners': includeClass('Partners.class.php'); return createObjAndAddToCol(new Partners()); break;case 'partners_category': includeClass('PartnersCategory.class.php'); return createObjAndAddToCol(new PartnersCategory()); break;case 'payment_confirmation': includeClass('PaymentConfirmation.class.php'); return createObjAndAddToCol(new PaymentConfirmation()); break;case 'payment_method': includeClass('PaymentMethod.class.php'); return createObjAndAddToCol(new PaymentMethod()); break;case 'payment_type': includeClass('PaymentType.class.php'); return createObjAndAddToCol(new PaymentType()); break;case 'point_cashback': includeClass('PointCashback.class.php'); return createObjAndAddToCol(new PointCashback()); break;case 'port': includeClass('Port.class.php'); return createObjAndAddToCol(new Port()); break;case 'portfolio': includeClass('Portfolio.class.php'); return createObjAndAddToCol(new Portfolio()); break;case 'portfolio_category': includeClass('PortfolioCategory.class.php'); return createObjAndAddToCol(new PortfolioCategory()); break;case 'preorder_header': includeClass('Preorder.class.php'); return createObjAndAddToCol(new Preorder()); break;case 'prepaid_expense': includeClass('PrepaidExpense.class.php'); return createObjAndAddToCol(new PrepaidExpense()); break;case 'project_dumper_header': includeClass('ProjectDumper.class.php'); return createObjAndAddToCol(new ProjectDumper()); break;case 'projects_header': includeClass('Projects.class.php'); return createObjAndAddToCol(new Projects()); break;case 'purchase_category': includeClass('PurchaseCategory.class.php'); return createObjAndAddToCol(new PurchaseCategory()); break;case 'purchase_order_header': includeClass('PurchaseOrder.class.php'); return createObjAndAddToCol(new PurchaseOrder()); break;case 'purchase_receive_header': includeClass('PurchaseReceive.class.php'); return createObjAndAddToCol(new PurchaseReceive()); break;case 'purchase_request_header': includeClass('PurchaseRequest.class.php'); return createObjAndAddToCol(new PurchaseRequest()); break;case 'purchase_return_header': includeClass('PurchaseReturn.class.php'); return createObjAndAddToCol(new PurchaseReturn()); break;case 'questionnaire_header': includeClass('Questionnaire.class.php'); return createObjAndAddToCol(new Questionnaire()); break;case 'questionnaire_response_header': includeClass('QuestionnaireResponse.class.php'); return createObjAndAddToCol(new QuestionnaireResponse()); break;case 'quiz_header': includeClass('Quiz.class.php'); return createObjAndAddToCol(new Quiz()); break;case 'quiz_result': includeClass('QuizResult.class.php'); return createObjAndAddToCol(new QuizResult()); break;case 'recruitment': includeClass('Recruitment.class.php'); return createObjAndAddToCol(new Recruitment()); break;case 'reminder': includeClass('Reminder.class.php'); return createObjAndAddToCol(new Reminder()); break;case 'rental_time_sheet_header': includeClass('RentalTimeSheet.class.php'); return createObjAndAddToCol(new RentalTimeSheet()); break;case 'revenue_cash_in': includeClass('RevenueCashIn.class.php'); return createObjAndAddToCol(new RevenueCashIn()); break;case 'rewards_point': includeClass('RewardsPoint.class.php'); return createObjAndAddToCol(new RewardsPoint()); break;case 'template_role': includeClass('RoleTemplate.class.php'); return createObjAndAddToCol(new RoleTemplate()); break;case 'routine_cost_header': includeClass('RoutineCost.class.php'); return createObjAndAddToCol(new RoutineCost()); break;case 'sales_car_service_return_header': includeClass('SalesCarServiceReturn.class.php'); return createObjAndAddToCol(new SalesCarServiceReturn()); break;case 'sales_delivery_header': includeClass('SalesDelivery.class.php'); return createObjAndAddToCol(new SalesDelivery()); break;case 'sales_order_header': includeClass('SalesOrder.class.php'); return createObjAndAddToCol(new SalesOrder()); break;case 'sales_order_car_service_header': includeClass('SalesOrderCarService.class.php'); return createObjAndAddToCol(new SalesOrderCarService()); break;case 'sales_order_dumper': includeClass('SalesOrderDumper.class.php'); return createObjAndAddToCol(new SalesOrderDumper()); break;case 'invoice_order_dumper_header': includeClass('SalesOrderDumperInvoice.class.php'); return createObjAndAddToCol(new SalesOrderDumperInvoice()); break;case 'sales_order_invoice_receipt_header': includeClass('SalesOrderInvoiceReceipt.class.php'); return createObjAndAddToCol(new SalesOrderInvoiceReceipt()); break;case 'sales_order_property_header': includeClass('SalesOrderProperty.class.php'); return createObjAndAddToCol(new SalesOrderProperty()); break;case 'sales_order_property_type': includeClass('SalesOrderPropertyType.class.php'); return createObjAndAddToCol(new SalesOrderPropertyType()); break;case 'sales_order_rental_header': includeClass('SalesOrderRental.class.php'); return createObjAndAddToCol(new SalesOrderRental()); break;case 'sales_order_rental_invoice_header': includeClass('SalesOrderRentalInvoice.class.php'); return createObjAndAddToCol(new SalesOrderRentalInvoice()); break;case 'sales_order_rental_work_order_header': includeClass('SalesOrderRentalWorkOrder.class.php'); return createObjAndAddToCol(new SalesOrderRentalWorkOrder()); break;case 'sales_order_subscription_header': includeClass('SalesOrderSubscription.class.php'); return createObjAndAddToCol(new SalesOrderSubscription()); break;case 'sales_rental_quotation_header': includeClass('SalesRentalQuotation.class.php'); return createObjAndAddToCol(new SalesRentalQuotation()); break;case 'item': includeClass('Service.class.php'); return createObjAndAddToCol(new Service()); break;case 'service_category': includeClass('ServiceCategory.class.php'); return createObjAndAddToCol(new ServiceCategory()); break;case 'shipment': includeClass('Shipment.class.php'); return createObjAndAddToCol(new Shipment()); break;case 'shipping_rate_header': includeClass('ShippingRate.class.php'); return createObjAndAddToCol(new ShippingRate()); break;case 'social_media': includeClass('SocialMedia.class.php'); return createObjAndAddToCol(new SocialMedia()); break;case 'stages_process': includeClass('StagesProcess.class.php'); return createObjAndAddToCol(new StagesProcess()); break;case 'item_category_storefront': includeClass('Storefront.class.php'); return createObjAndAddToCol(new Storefront()); break;case 'supplier': includeClass('Supplier.class.php'); return createObjAndAddToCol(new Supplier()); break;case 'supplier_downpayment': includeClass('SupplierDownpayment.class.php'); return createObjAndAddToCol(new SupplierDownpayment()); break;case 'supplier_downpayment_settlement_header': includeClass('SupplierDownpaymentSettlement.class.php'); return createObjAndAddToCol(new SupplierDownpaymentSettlement()); break;case 'survey_question': includeClass('Survey.class.php'); return createObjAndAddToCol(new Survey()); break;case 'tag_detail': includeClass('Tag.class.php'); return createObjAndAddToCol(new Tag()); break;case 'template_customer_header': includeClass('TemplateCustomer.class.php'); return createObjAndAddToCol(new TemplateCustomer()); break;case 'template_emkl_purchase_item_header': includeClass('TemplateEMKLPurchaseItem.class.php'); return createObjAndAddToCol(new TemplateEMKLPurchaseItem()); break;case 'template_supplier_header': includeClass('TemplateSupplier.class.php'); return createObjAndAddToCol(new TemplateSupplier()); break;case 'term_of_payment': includeClass('TermOfPayment.class.php'); return createObjAndAddToCol(new TermOfPayment()); break;case 'terminal': includeClass('Terminal.class.php'); return createObjAndAddToCol(new Terminal()); break;case 'termination': includeClass('Termination.class.php'); return createObjAndAddToCol(new Termination()); break;case 'testimonial': includeClass('Testimonial.class.php'); return createObjAndAddToCol(new Testimonial()); break;case 'ticket_support_header': includeClass('TicketSupport.class.php'); return createObjAndAddToCol(new TicketSupport()); break;case 'ticket_support_work_order': includeClass('TicketSupportWorkOrder.class.php'); return createObjAndAddToCol(new TicketSupportWorkOrder()); break;case 'time_unit': includeClass('TimeUnit.class.php'); return createObjAndAddToCol(new TimeUnit()); break;case 'trucking_cost_cash_in_header': includeClass('TruckingCostCashIn.class.php'); return createObjAndAddToCol(new TruckingCostCashIn()); break;case 'trucking_cost_cash_out_header': includeClass('TruckingCostCashOut.class.php'); return createObjAndAddToCol(new TruckingCostCashOut()); break;case 'sales_order_invoice_receipt_header': includeClass('TruckingInvoiceReceipt.class.php'); return createObjAndAddToCol(new TruckingInvoiceReceipt()); break;case 'trucking_job': includeClass('TruckingJob.class.php'); return createObjAndAddToCol(new TruckingJob()); break;case 'trucking_purchase_header': includeClass('TruckingPurchase.class.php'); return createObjAndAddToCol(new TruckingPurchase()); break;case 'trucking_selling_rate_header': includeClass('TruckingSellingRate.class.php'); return createObjAndAddToCol(new TruckingSellingRate()); break;case 'trucking_service_order_header': includeClass('TruckingServiceOrder.class.php'); return createObjAndAddToCol(new TruckingServiceOrder()); break;case 'trucking_service_order_category': includeClass('TruckingServiceOrderCategory.class.php'); return createObjAndAddToCol(new TruckingServiceOrderCategory()); break;case 'trucking_service_order_invoice_header': includeClass('TruckingServiceOrderInvoice.class.php'); return createObjAndAddToCol(new TruckingServiceOrderInvoice()); break;case 'trucking_service_work_order': includeClass('TruckingServiceWorkOrder.class.php'); return createObjAndAddToCol(new TruckingServiceWorkOrder()); break;case 'vendor_warranty_claim_header': includeClass('VendorWarrantyClaim.class.php'); return createObjAndAddToCol(new VendorWarrantyClaim()); break;case 'vendor_warranty_claim_return_header': includeClass('VendorWarrantyClaimReturn.class.php'); return createObjAndAddToCol(new VendorWarrantyClaimReturn()); break;case 'vessel': includeClass('Vessel.class.php'); return createObjAndAddToCol(new Vessel()); break;case 'voucher': includeClass('Voucher.class.php'); return createObjAndAddToCol(new Voucher()); break;case 'voucher_trade_point_header': includeClass('VoucherTradePoint.class.php'); return createObjAndAddToCol(new VoucherTradePoint()); break;case 'voucher_transaction': includeClass('VoucherTransaction.class.php'); return createObjAndAddToCol(new VoucherTransaction()); break;case 'warehouse': includeClass('Warehouse.class.php'); return createObjAndAddToCol(new Warehouse()); break;case 'warehouse_transfer_header': includeClass('WarehouseTransfer.class.php'); return createObjAndAddToCol(new WarehouseTransfer()); break;case 'warranty_claim_header': includeClass('WarrantyClaim.class.php'); return createObjAndAddToCol(new WarrantyClaim()); break;case 'warranty_claim_progress_header': includeClass('WarrantyClaimProgress.class.php'); return createObjAndAddToCol(new WarrantyClaimProgress()); break;case 'warranty_period': includeClass('WarrantyPeriod.class.php'); return createObjAndAddToCol(new WarrantyPeriod()); break;case 'waste': includeClass('Waste.class.php'); return createObjAndAddToCol(new Waste()); break;case '_widget_setting': includeClass('WidgetSetting.class.php'); return createObjAndAddToCol(new WidgetSetting()); break;case 'work_progress_detail': includeClass('WorkProgress.class.php'); return createObjAndAddToCol(new WorkProgress()); break;case 'work_progress_step': includeClass('WorkProgressStep.class.php'); return createObjAndAddToCol(new WorkProgressStep()); break;case 'youtube': includeClass('Youtube.class.php'); return createObjAndAddToCol(new Youtube()); break;case 'business_unit': includeClass('BusinessUnit.class.php'); return createObjAndAddToCol(new BusinessUnit());case 'asset_item_purchase_request_header': includeClass('AssetItemPurchaseRequest.class.php'); return createObjAndAddToCol(new AssetItemPurchaseRequest());case 'asset_item_purchase_header': includeClass('AssetItemPurchase.class.php'); return createObjAndAddToCol(new AssetItemPurchase());case 'asset_item': includeClass('AssetItem.class.php'); return createObjAndAddToCol(new AssetItem());case 'asset_item_purchase_receive_header': includeClass('AssetItemPurchaseReceive.class.php'); return createObjAndAddToCol(new AssetItemPurchaseReceive());case 'sales_order_item_asset_header': includeClass('SalesOrderItemAsset.class.php'); return createObjAndAddToCol(new SalesOrderItemAsset());case 'sales_work_order_item_asset_header': includeClass('SalesWorkOrderItemAsset.class.php'); return createObjAndAddToCol(new SalesWorkOrderItemAsset());case 'service_purchase_order_header': includeClass('ServicePurchaseOrder.class.php'); return createObjAndAddToCol(new ServicePurchaseOrder());case 'recurring_period': includeClass('RecurringPeriod.class.php'); return createObjAndAddToCol(new RecurringPeriod());case 'sales_order_rental_item_asset_header': includeClass('SalesOrderRentalItemAsset.class.php'); return createObjAndAddToCol(new SalesOrderRentalItemAsset());case 'work_order_rental_item_asset_header': includeClass('WorkOrderRentalItemAsset.class.php'); return createObjAndAddToCol(new WorkOrderRentalItemAsset()); case 'quotation_sales_order_item_asset_header': includeClass('QuotationSalesOrderItemAsset.class.php'); return createObjAndAddToCol(new QuotationSalesOrderItemAsset()); break; case 'building_unit': includeClass('BuildingUnit.class.php'); return createObjAndAddToCol(new BuildingUnit()); case 'trucking_purchase_refund': includeClass('TruckingPurchaseRefund.class.php'); return createObjAndAddToCol(new TruckingPurchaseRefund());case 'tax': includeClass('Tax.class.php'); return createObjAndAddToCol(new Tax());case 'vat_out_header': includeClass('VatOut.class.php'); return createObjAndAddToCol(new VatOut());case 'amortization_header': includeClass('Amortization.class.php'); return createObjAndAddToCol(new Amortization());
         case 'template_emkl_job_order': includeClass('TemplateEMKLJobOrder.class.php'); return createObjAndAddToCol(new TemplateEMKLJobOrder());
         case 'debit_note_header': includeClass('DebitNote.class.php'); return createObjAndAddToCol(new DebitNote());
            break;
        }


        return null;
    }
    
    
    function getTransactionLogDetailReportByTableKey($arrList){
        
        
        $exclude = array('car_turnover');
        
        $arr = array();
        foreach($arrList as $row){ 
            $index = $row['tablename']; 
            
            // beberapa table hanya mencatat movement, jd harus dikeluarkan
            if(in_array($index,$exclude)) continue;
            
            if(!isset($arr[$index])) $arr[$index] = array();
            
            if (!in_array($row['refkey'], $arr[$index]))
                array_push($arr[$index], $row['refkey']);
                 
        }
		 
        
        foreach($arr as $key=>$row){
            $tableName = $key; 
            $obj = $this->getObjMapping($tableName);
             // native biar yg diselect dikit aj
             // beberapa table gk ad status
             //$hasStatusField = (isset($obj->tableStatus) && !empty($obj->tableStatus)) ?  true : false;
             
			$sql = 'select 
                            '.$tableName.'.pkey,
                            '.$tableName.'.code 
                         from 
                            '.$tableName.'
                        where  
                            '.$tableName.'.pkey in ('.implode(',',$row).')'; 
             
             $rs = $this->oDbCon->doQuery($sql); 
			
			
			 $sql = 'select * from ' . $obj->tableStatus ;
         
             $rsStatus = $this->oDbCon->doQuery($sql); 
			 $rsStatus = array_column($rsStatus,null,'pkey');
			 
             $arr[$key] = array_column($rs,null,'pkey');
			 $arr[$key]['arrStatus'] = $rsStatus;
        }
         
        return  $arr;
    }
     
    function arrayColumnWithMultiDimension($arr, $key){
        
        $newArr = array();
        
        foreach($arr as $row){
            if (!isset($row[$key])) continue;
            
            $keyIndex = $row[$key];
            if (!isset($newArr[$keyIndex]))
                $newArr[$keyIndex] = array();
            
            array_push($newArr[$keyIndex], $row); 
        }
        
        //$this->setLog($newArr,true);
        return $newArr;
        
    }
    
    function getPrevData($tableName, $id){ 
        $prevData = 'select * from '.$tableName.' where pkey = ' . $this->oDbCon->paramString($id);
        $rsPrev = $this->oDbCon->doQuery($prevData); 
        return $rsPrev[0];
    }
      
    
    function arrayToXML($arrData, &$xmlData) {
        foreach($arrData as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xmlData->addChild($key);
                    $this->arrayToXML($value, $subnode);
                } else {
                    $this->arrayToXML($value, $xmlData);
                }
            }
            else {
                //$key = is_numeric($key) ? 'item'.$key : $key;
                $xmlData->addChild($key,$value);
            }
        }
    }
    
    function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    function getRepeatPeriod($pkey=''){
        $sql = 'select * from repeat_periode where statuskey = 1';
		
		if(!empty($pkey))
			$sql .= ' and pkey = '. $this->oDbCon->paramString($pkey);
		
        return $this->oDbCon->doQuery($sql); 
    }
    
    function checkCronJobSchedule($obj,$pkey){
        
        $rs = $obj->getDataRowById($pkey);
        $repeattypekey = $rs[0]['repeattypekey']; 
        
        $dateRepeat = $this->formatDBDate($rs[0]['repeatdate'],'d');
        $monthRepeat = $this->formatDBDate($rs[0]['repeatdate'],'n');
        
        $now = time();
        $dateNow =  date("d",$now);
        $monthNow =  date("n",$now); 
        $lastDay = date("t",$now); // 28 - 31
        
        switch($repeattypekey){
            case  CRON_REPEAT['daily']: break; // blm aktif
            case  CRON_REPEAT['monthly']:  // cek kalo tgl hr ini lebih gk punya tgl 31 atau 30 atau 29
                                            // $lastDay (28) < $dateRepeat (31)
                                            if($dateNow == $dateRepeat || $lastDay < $dateRepeat ) return true;
                                            break;
            case  CRON_REPEAT['annually']: 
                                            break;
                
        }
      
        return false;
    }
    
    function runDefaultSystemCronJob(){
         
        $licenseCon = $this->masterConn(); 


        $sql = 'select 
                    customer_company.name
                from 
                    customer_company 
				where 
					customer_company.statuskey = 2
                ';


        $rs = $licenseCon->doQuery($sql);  
        $licenseCon = null;

        foreach($rs as $row){ 
                // harus reset ulang
                $arrAction = array('updateDiskUsage');
            
                $name = $row['name'];
            
                // sementara
                if(IS_DEVELOPMENT) $name = 'minerva.local';

                $basepath = 'https://'.$name.'/cron/'; 
    
                foreach( $arrAction as $action){
                    $url = $basepath.$action; 
                    //$this->setLog('['.date("Y/m/d H:i").'] '.$url,true,'cronlog');

                    $header = array(
                        'Content-Type: application/json', 
                    );

                    $payload = array('mnv-cron' => 1); 
                    $payload = urldecode(http_build_query($payload)); 

                    $connection = curl_init(); 
                    curl_setopt($connection, CURLOPT_URL, $url); 
                    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
                    curl_setopt($connection, CURLOPT_POST, 1);  
                    curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
                    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($connection); 
                    curl_close($connection); 
                }


        }
  
    }
	
	
    function runCronJob(){
         
        $licenseCon = $this->masterConn(); 

        $sql = 'select 
                    customer_company.name,
                    customer_company_cronjob.*
                from 
                    customer_company, customer_company_cronjob
                where 
					customer_company.pkey = customer_company_cronjob.refkey and 
					customer_company.statuskey = 2
                ';


        $rs = $licenseCon->doQuery($sql);  
        $licenseCon = null;

        foreach($rs as $row){ 
                // harus reset ulang
                $arrAction = array(); //array('updateDiskUsage');
            
                $name = $row['name'];
            
                // sementara
                if(IS_DEVELOPMENT) $name = 'minerva.local';

                $basepath = 'https://'.$name.'/cron/'; 

                if(!empty($row['cronfile'] )){ 
            
                    $allowToCroned = false;
                    
                    switch($row['intervalkey']){
                        case 3 : // jika bulanan, tgl harus sesuai 
                                 if ( $row['ondate'] ==  date('d')) $allowToCroned = true;
                                 break;    
                        case 2 : // jika harian, jam harus sesuai 
                                 if ( $row['onhour'] == (int) date('h')) $allowToCroned = true;
                                 break;    
                        default : $allowToCroned = true; 
                                  break;
                    }

                    if($allowToCroned)  array_push($arrAction, $row['cronfile']);
                     
                }
            

//				$this->setLog($basepath,true);
//				$this->setLog($arrAction,true);

                foreach( $arrAction as $action){
                    $url = $basepath.$action; 
//                    $this->setLog('['.date("Y/m/d H:i").'] '.$url,true,'cronlog.txt');

                    $header = array(
                        'Content-Type: application/json', 
                    );

                    $payload = array('mnv-cron' => 1); 
                    $payload = urldecode(http_build_query($payload)); 

                    $connection = curl_init(); 
                    curl_setopt($connection, CURLOPT_URL, $url); 
                    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
                    curl_setopt($connection, CURLOPT_POST, 1);  
                    curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
                    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($connection); 
                    curl_close($connection); 
                }


        }
  
    }
    
    function checkPasswordStrength($password,$requirements = ['uppercase', 'lowercase', 'number', 'special', 'length']) {
       
            // Validate password strength
            //$uppercase = preg_match('@[A-Z]@', $password);
            //$lowercase = preg_match('@[a-z]@', $password);
            //$number    = preg_match('@[0-9]@', $password);
            //$specialChars = preg_match('@[^\w]@', $password);
//
            //return (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8 || strlen($password) > 30) ? false : true;
        
    //}
//
    //return true;
//}
        
         
            $checks = [
                'uppercase' => preg_match('/[A-Z]/', $password),
                'lowercase' => preg_match('/[a-z]/', $password),
                'number'    => preg_match('/[0-9]/', $password),
                'special'   => preg_match('/[^\w]/', $password),
                'length'    => strlen($password) >= 8 && strlen($password) <= 30,
            ];
 
            foreach ($requirements as $requirement) {
                if (!isset($checks[$requirement]) || !$checks[$requirement]) {
                    return false;
                }
            }
   
        return true;
        
    }
    
    
    //function createQR($data,$matrixPointSize = 10, $phpThumb=array(), $storage = ''){
//
    //    // QR
    //    //set it to writable location, a place for temp generated PNG files
    //    $PNG_TEMP_DIR = $this->uploadTempDoc.'qr/';
//
    //    //ofcourse we need rights to create temp dir
    //    if (!is_dir($PNG_TEMP_DIR))
			//mkdir ($PNG_TEMP_DIR,  0755, true);
//
    //    require_once DOC_ROOT.'/assets/qrlib/qrlib.php';    
//
    //    $errorCorrectionLevel = 'H'; //array('L','M','Q','H'  
//
    //    $filename = md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
    //    QRcode::png($data, $PNG_TEMP_DIR.$filename, $errorCorrectionLevel, $matrixPointSize, 2);    
//
    //    $size = (isset($size) && !empty($size)) ? $size : 60;
		//
		//$returnArr = array();
		//$returnArr['filename'] = $filename;
		//$returnArr['url'] = $this->uploadTempDocShort.'qr/'.$filename;
		//
		//if(!empty($phpThumb))
			//$returnArr['url_phpthumb'] = HTTP_HOST.'phpthumb/phpThumb.php?src='.$this->uploadTempDocShort.'qr/'.$filename.'&w='.$phpThumb['w'].'&h='.$phpThumb['h'].'&f=png&hash='.getPHPThumbHash($filename);
			//
		//return $returnArr;
    //}
    
    function createQR($data,$matrixPointSize = 10, $opt=array()){

        // nanti tambahkan opt bisa overrride filename
        
        require_once DOC_ROOT.'/assets/qrlib/qrlib.php';    

        $errorCorrectionLevel = 'H'; //array('L','M','Q','H'  

        //$filename = md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
        if(isset($opt['fileName']) && !empty($opt['fileName']))
            $filename = $opt['fileName'];
        else
            $filename = md5(strtolower($data)).'.png'; // harus di md5 utk mencegah karakter aneh, seperti '/'
        
        ob_start();
        QRcode::png($data,null, $errorCorrectionLevel, $matrixPointSize, 2);    
        $qrBinary = ob_get_clean();

        if(isset($opt['useStorage']) && $opt['useStorage'] == true){ 
            $client = $this->initS3Client(); 
            $acl = 'private';
            
            $uploadQRFolder = (isset($opt['uploadFolder']) && !empty($opt['uploadFolder'])) ? $opt['uploadFolder'] : $this->uploadQRFolder;
                
            $client->putObject([
                 'Bucket' => STORAGE['bucket'],
                 'Key'    => DOMAIN_NAME.'/'.$uploadQRFolder.$filename,
                 'Body'   => $qrBinary,
                 'ACL'    =>  $acl, // set private by default
                 'ContentType' => 'image/png',   
            ]);

        }else{

            // QR
            //set it to writable location, a place for temp generated PNG files
            $PNG_TEMP_DIR = $this->uploadTempDoc.'qr/'; 
            //ofcourse we need rights to create temp dir
            if (!is_dir($PNG_TEMP_DIR))  mkdir ($PNG_TEMP_DIR,  0755, true);
            
            file_put_contents($PNG_TEMP_DIR.$filename, $qrBinary);
            
            $returnArr = array();
            $returnArr['filename'] = $filename;
            $returnArr['url'] = $this->uploadTempDocShort.'qr/'.$filename;

            if(isset($opt['phpthumb']) && !empty($opt['phpthumb']))
                $returnArr['url_phpthumb'] = HTTP_HOST.'phpthumb/phpThumb.php?src='.$this->uploadTempDocShort.'qr/'.$filename.'&w='.$opt['phpthumb']['w'].'&h='.$opt['phpthumb']['h'].'&f=png&hash='.getPHPThumbHash($filename);

        }
		
			
		return $returnArr;
    }
    
    function getQueryForList(){
        return '';
    }
    
    function getDateUsedForTimestamp($tableName, $rs ){
        // 1 = trdate
        // 2 = confirmeddate
        
        $sql = 'select timestamptype from '.$this->tablekey.' where tablename = ' .$this->oDbCon->paramString($tableName) ; 
        $rsTimeStamp  = $this->oDbCon->doQuery($sql); 
        
        $timeStampType = (!empty($rsTimeStamp)) ? $rsTimeStamp[0]['timestamptype'] : 1;
        
        $timestamp = ( $timeStampType == TIME_STAMP_TYPE['confirmeddate'] || !isset($rs[0]['trdate']) ) ? date('Y-m-d') : $rs[0]['trdate'] ;
        
        return array(
                'timestampType' => $timeStampType,
                'timestamp' => $timestamp,
        );
        
    }
    
    function setActiveLang(){
        // $userkey khusus utk admin / user frontend yg login
        // sementara diabaikan
        
        // get default lang,
        if(defined('THEME_SETTINGS') && isset(THEME_SETTINGS['lang'])){
            //kalo ada Theme (admin), udah pasti pake themes
            $this->langCode = THEME_SETTINGS['lang']['value']; 
        }else{
            $this->langCode = $this->loadSetting('defaultLang');  
        
            // kalo sudah pernah diset sebelumnya
            // harusnya sementara front end saja yg ini
            if(isset($_SESSION['lang']) && !empty($_SESSION['lang']) ){ 
                if($_SESSION['lang'] <> $this->langCode)
                     $this->langCode = $_SESSION['lang'];
            }

        }   
    
        // LANG  
        $this->langCode = (!file_exists ( DOC_ROOT. 'include/lang/'.$this->langCode.'.php' )) ? 'id' : $this->langCode;
       
        include(DOC_ROOT. 'include/lang/'.$this->langCode.'.php');  
 
        // add lang per domain 
        //$domainLangFile = DOC_ROOT. 'template/'.DOMAIN_NAME.'/lang/'.LANG.'.php';
        $domainLangFile =  PERSONALIZED_DOC_PATH.'include/lang/'.$this->langCode.'.php';
        if (file_exists($domainLangFile)) include($domainLangFile);  
 
    }
    
    function updateOthersLangValue($arrParam,$structureData){
         
        $this->includeClassDependencies('Lang.class.php');
             
        $lang = new Lang();
        $rsLang = $lang->searchData($lang->tableName.'.statuskey',1, true, ' and '.$lang->tableName.'.systemVariable <> 1');
        $arrLang = array_column($rsLang,'pkey');
        
        //set LANG details
        $rsObj = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        // nanti coba test, kalo sudah dipake hidDetailKey, maka ubah parameternya
        $elName = 'hidDetailKey';
        if(isset($arrParam['hidDetailKey'])){
            $elName = 'hidLangDetailKey';
            
            $this->arrDataLang['pkey'] = array($elName);
            

            //   $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            // array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
            
            $arrDetails =  $this->arrData['pkey'][1]['dataDetail'];
            $totalArrDetail =  count($arrDetails);
            
            for($i=0;$i<$totalArrDetail;$i++){
                if($arrDetails[$i]['tableName'] == $this->tableLangValue)
                    $this->arrData['pkey'][1]['dataDetail'][$i]['dataset'] = $this->arrDataLang; 
            }
        }
        
        // lupa kenapa awalnya dicek dulu
        $ctr = (isset($arrParam[$elName]))  ? count($arrParam[$elName]) : 0;

        $totalDetails = 0;  
        foreach($arrLang as $lang){ 
                     
                foreach($structureData as $key => $row){ 
                    
                    $langObjName = $row[0].'-'.$lang; 
                    
                    // kalo gk pake multi lang 
                    if (!isset($arrParam[$langObjName])) continue; 
                     
                    $arrParam[$elName][$ctr] = 0;
                    $arrParam['refTableType'][$ctr] = $rsObj['key'];
                    $arrParam['hidLangKey'][$ctr] = $lang; 
                    $arrParam['columnName'][$ctr] = $key;
                    
                    // kalo raw
                    if(isset($row[1]) && $row[1] == 'raw') 
                        $arrParam[$langObjName] =  '\''.addslashes($arrParam[$langObjName]).'\''; 
                    else 
                        $arrParam[$langObjName] =  $this->oDbCon->paramString($arrParam[$langObjName]);
                  
                     
                    $arrParam['langValue'][$ctr] = $arrParam[$langObjName];
                    
                    $ctr++;
                    $totalDetails++;
                } 
        }
        
        if (!isset($arrParam['hidTotalRows'])) {
            $arrParam['hidTotalRows'] = array();
            $arrParam['hidTotalRows'][0] = array();
        }
    
        array_push($arrParam['hidTotalRows'][0],$totalDetails);
        
        return $arrParam;
    }
    
 
    
    function postLangValue($id,$textField){ 
        $rsObj = $this->getTableKeyAndObj($this->tableName, array('key'));
        
        $sql = 'select * from '.$this->tableLangValue.' where reftabletype = '.$this->oDbCon->paramString($rsObj['key']).' and refkey = ' . $this->oDbCon->paramString($id);
        $rs = $this->oDbCon->doQuery($sql); 
        
        foreach($rs as $row) {    
            $elName = $this->arrData[$row['columnname']][0];   
            $_POST[$elName.'-'.$row['reflangkey']] = $this->HTMLSpecialCharacterForEditor($row['value']);     
        } 
    }
    
    function loadData($rs){
        if (!isset($this->newLoad) || !$this->newLoad) return;
        
        foreach($this->arrData as $elName => $row){
            
            if(isset($row[1])){
                // beberapa modelnya array
                $format = $row[1];
                    
                switch($format){
                    case 'decimal' :    $rs[0][$elName] = $this->formatNumber($rs[0][$elName],2);
                                        break;
                    case 'integer' :   
                    case 'number' :  $rs[0][$elName] = $this->formatNumber($rs[0][$elName]);
                                      break;
                    case 'date' :    $rs[0][$elName] = $this->formatDBDate($rs[0][$elName]);
                                      break; 
                    case 'monthperiod' :    $rs[0][$elName] = $this->formatDBDate($rs[0][$elName],'F Y');
                                      break; 
                    case 'datetime' :    $rs[0][$elName] = $this->formatDBDate($rs[0][$elName],'d / m / y H:i');
                                      break; 
                    case 'time' :    $rs[0][$elName] = $this->formatDBDate($rs[0][$elName],'H:i');
                                      break; 
                }
                
            } 
                
            $_POST[$row[0]] = $rs[0][$elName];
         
        }
             
        $this->postLangValue($rs[0]['pkey'],'name');
 
    }
    
     function sumArrayColumnGroup($arrDetail,$groupby, $isnumeric = array()){ 
        
        $newArray = array();
        foreach($arrDetail as $row){
            
            $groupkey = $row[$groupby];
            
            if(!isset($newArray[$groupkey]))
               $newArray[$groupkey] = $row;
            else{
                
                foreach($row as $key => $value){
                    if(empty($value))  continue;
                    
                    if(in_array($key,$isnumeric))
                      $newArray[$groupkey][$key] += $value;
                    else
                      $newArray[$groupkey][$key] = $newArray[$groupkey][$key].', '.$value;  
                    
                }
                
            }
                  
                
        }
        
        return $newArray;
     
    }
    
    
    function generateLangOptions(){
        
        $lang = new Lang();
        
        $rsLang = $lang->searchData($lang->tableName.'.statuskey',1);
        if (count($rsLang) <= 1) return;
        
        $arrLang = $this->convertForCombobox($rsLang,'pkey','name',$this->lang['allLanguages']);
        
        $_POST['mnvSelLang'] = $rsLang[0]['pkey'];
            
        $html = '<div class="mnv-lang-panel">
                    <div class="div-table">
                         <div class="div-table-row">
                         <div class="div-table-col-3" style="vertical-align:center">'. $this->lang['language'] .'</div>
                         <div class="div-table-col-3">'. $this->inputSelect('mnvSelLang', $arrLang) .'</div>
                         </div>
                    </div> 
                 </div>
                 <div style="clear:both; height: 3em"></div>
                 ';
        
        return $html;
    }
    
    function updateContentLang($rs,$langCode='', $arrOptAutoTransalate = array()){
        
        if (empty($langCode))
            $langCode = $this->langCode;
        
        $useAutoTranslate = (!empty($arrOptAutoTransalate)) ? true : false;
            
        if($useAutoTranslate){
              
            // mbil dulu semua field yg perlu ditranslate, biar sekalian kirim
            // paling tidak harus sekali save dulu diadmin, biar ke catet kolom apa saja
                $arrColumn = $arrOptAutoTransalate['column'];
            
                includeClass('GCloud.class.php');
                $GCloud = new GCloud();

                foreach($arrColumn as $columnName){ 
                    $arrToTranslate = array();
                    
                    foreach($rs as  $dataRow) 
                       array_push($arrToTranslate, $dataRow[$columnName]);
                 
                    $result = $GCloud->translate($arrToTranslate,$langCode);
                    
                    foreach($rs as $key => $dataRow) 
                         $rs[$key][$columnName] = $result[$key];
                
                }
                 
            
        }else{
            $rsObj = $this->getTableKeyAndObj($this->tableName, array('key'));
            $tablekey = $rsObj['key'];

            foreach($rs as $key => $dataRow){ 

                $pkey = $dataRow['pkey'];

                $sql = 'select 
                            '.$this->tableLangValue.'.columnname,
                            '.$this->tableLangValue.'.value
                        from 
                            '.$this->tableLangValue.',
                            '.$this->tableLang.'                
                        where 
                            '.$this->tableLangValue.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' and 
                            '.$this->tableLangValue.'.refkey = ' . $this->oDbCon->paramString($pkey).' and 
                            '.$this->tableLang.'.code = ' . $this->oDbCon->paramString($langCode).' and 
                            '.$this->tableLangValue.'.reflangkey =  '.$this->tableLang.'.pkey ' ;


                $rsContentLang = $this->oDbCon->doQuery($sql); 

                foreach($rsContentLang as $row)
                    if(!empty($row['value']))
                        $rs[$key][$row['columnname']] = $row['value'];

            }


        }
        
        return $rs;
    }
    
    function updateTransactionChangedLog($oldDataset, $newDataset){ 
        /*
          $arrExclude = array('pkey','modifiedon', 'modifiedby');
        
          $refkey = $oldDataset['pkey'];
        
          // kalo ad data / detail yg kehapus, gk ketauan...
          // karena patokanyna dr nilai baru 
        
          foreach($newDataset as $row){
            
             $key = $row['field'];
             if (in_array($key,$arrExclude)) continue;
              
            // !! nilai newValue sudah di paramString
            // harus di cek kalo oldValue di paramString utk tipe tgl masalah gk ??  
             
             $field = $row['field'];
             $tablekey = $row['tablekey'];
             $currValue = $row['value'];
             $oldValue = $oldDataset[$key];
             
              
             $changed = false;
             switch($row['format']){
                    case 'decimal' :   
                    case 'integer' :   
                    case 'number' :   $changed = (str_replace('\'','',$currValue) <> $oldValue) ? true : false; // mungkin akan ad masalah kalo decimalnya panjang
                                      break;
                    case 'date' :   $changed = ($this->formatDBDate(str_replace('\'','',$currValue),'Y-m-d') <> $oldValue) ? true : false; // mungkin akan ad masalah kalo decimalnya panjang
                                      break;
                    default :  $changed = ($currValue <> $this->oDbCon->paramString($oldValue)) ? true : false;
             }
            
              
             if ($changed){
                 $sql = 'insert into '.$this->transactionLogDetail.' 
                            (tablekey,transactionkey,fieldname,oldvalue,newvalue) 
                        values (
                            '.$this->oDbCon->paramString($tablekey).', 
                            '.$this->oDbCon->paramString($refkey).', 
                            '.$this->oDbCon->paramString($field).', 
                            '.$this->oDbCon->paramString($oldValue).', 
                            '.$currValue.' 
                        )';
                 
                 //$this->setLog($sql,true);
                 $this->oDbCon->execute($sql);
             }
         }*/
    }
    
    function importAPIArrayCleansing($value){
        
        $arrReturn = array();
        
        foreach($value as $valueRow){
            $arrTemp = array();
            
            foreach($valueRow as $key => $row){ 
                if (!is_array($row))
                    $arrTemp[$key] = $row;
                else 
                    $arrTemp[$key] = (is_array($row['value'])) ? $this->importAPIArrayCleansing($row['value']) : $row['value'];
            }
            
            array_push($arrReturn,$arrTemp);
        }
        
        return $arrReturn;
    }
         
    function executeImportAPI($url,$data,$code=''){   
         
        if (empty($data)) die; 
        
        $payload = array(); 
		//$this->setLog($data,true,'payload.txt');
		
        foreach($data as $key=>$row){ 
            
            // ad kemungkinan $value ini array detail
            $value = $row['value'];
            
            if(is_array($value))
                $value = $this->importAPIArrayCleansing($value);
            
            $payload[$key] = $value; 
             
            // kalo tipenya array data
            
            // kalo refkey, cek udah ad blm, kalo blm ad add... 
            if (isset($row['data']['ref']) && !empty($row['data']['ref'])){
                
                // kalo gk ad nilainya, continue
                if(empty($value)) continue;
                
                $ref = $row['data']['ref'];
                //$this->setlog('intest',true);
               
                $tabletypekey = $ref['reftabletype'];
                $obj = $this->getTableNameAndObjById($tabletypekey)['obj']; 
                 
                // by default pilih kolom name
                $columnName = (isset($ref['columnName']) && !empty($ref['columnName']) ) ? $ref['columnName'] : 'name'; 
                
                
                $rs = $obj->searchData($obj->tableName.'.'.$columnName,$value,true);
                
                // overwrite value
                if (empty($rs)){   
                
                    if (isset($ref['apiURL']) && !empty($ref['apiURL'])){
                        // add data baru 
                        // sementara berdasarkan nama kolom dulu

                        $newPayload = array(  
                            'name' => $value
                            // statuskey gk perlu ditambahkan, tambahkan nilai default saja di table
                        ); 

                        //$newPayload = urldecode(http_build_query($newPayload)); 
						$newPayload = json_encode($newPayload);
						
                        // header AUTH    
						$this->setLog('>> perlu dicek lg auth nya, kalo add sub disini', true);
                        $secretkey = $this->loadSetting('minervaSecretKey'); 
                        $auth = hash_hmac('sha256', API_URL.$ref['apiURL'] . '|' . $newPayload, $secretkey); 
						
                        $header = array(
                            'Content-Type: application/json', 
                            //'Content-Length: ' . strlen($payload),
                            'Authorization: '.$auth
                        );

						
                        $connection = curl_init(); 
                        curl_setopt($connection, CURLOPT_URL, API_URL.$ref['apiURL']); 
                        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
                        curl_setopt($connection, CURLOPT_HTTPHEADER,$header);
                        curl_setopt($connection, CURLOPT_POSTFIELDS, $newPayload); 
                        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
                        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");

                        $response = curl_exec($connection);
                        curl_close($connection); 

                        $response = json_decode($response,true); 
                        $payload[$key] = $response['data'][$columnName];

                    }else{
                        // return error karena data tidak ditemukan 
                        $payload[$key] = '';
                    } 
                    
                }
                
            }
        }

        // kalo ada detail nanti perlu dipikirkan lg
 
        // tentuin POST / PUT 
        // selalu dr field 'code' 
        $method = 'POST'; 
        
        if (!empty($code) && isset($data[$code])){ 
            $keyValue = urldecode($data[$code]['value']);
            $rs = $this->isValueExisted('',$code,$keyValue);
            if (!empty($rs)) $method = 'PUT'; 
        }
 
        // GK BOLEH diunset, karena kemungkinan memang mau hapus nilai fieldnya
        // tp gk tau masalah gk di EMKL
        /*foreach($payload as $key=>$row){ 
            $this->setLog($key.'=>'.$row,true);
            if ($row == '')  unset($payload[$key]);
        }*/
         
       //$this->setLog($method,true); 
       //$this->setLog($payload,true); 
      

        // START CALL API >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 
            
		//$this->setLog($payload,true,'payload.txt');
		
        //$payload = urldecode(http_build_query($payload));  
		$payload = json_encode($payload,true);  // update paek json saja
		
        //$userkey = (isset($data['_userkey']['value'])) ? $data['_userkey']['value'] : ''; // dari SESSION saja, kira2 ad masalah tdk kalo dr session
		$userkey = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        
        // getUserOTP, menyamakan dengan apps, 
        // asumsi hanya user yg punya akses sudah di validasi dihalaman import
        $usertoken =  (!empty($userkey)) ? $this->getUserOTP($userkey) : '';
        
        // header AUTH    
        $secretkey = $this->loadSetting('minervaSecretKey'); 
        //$auth = hash_hmac('sha256', $url . '|' . $payload . $usertoken, $secretkey); 
		$auth = hash_hmac('sha256',  $url,$secretkey).'.'.$userkey.'.'.$usertoken;  
		 
		
		//$this->setLog('>> send ',true);
		//$this->setLog($auth,true);
		
        $header = array(
            'Content-Type: application/json', 
            //'Content-Length: ' . strlen($payload),
            'Authorization: '.$auth
        );


		//$this->setLog($payload,true,'payload.txt');
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($connection, CURLOPT_HTTPHEADER,$header);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);

        $response = curl_exec($connection);
        curl_close($connection);
        //$this->setLog($response,true); 
        return  $response;
        
    }
     
    function getUserOTP($userkey,$includeLastToken = false, $accountType = 0){
        
        // $accountType utk jenis akun, employee atau customer misalnya
        
        switch($accountType){
            case 1 : $tableName = $this->tableCustomer; break;
            default : $tableName = $this->tableEmployee; break;
        }
        
        // ini dipake oleh Apps, yg sudah ad informasi pass nya / sudah login
        // kalo dr web bagaimana ? yg gk ad informasi passwordnya.. (pake yg bawah)
        
        $sql = 'select password from '.$tableName.' where pkey = '. $this->oDbCon->paramString($userkey);  
        
        $rs = $this->oDbCon->doQuery($sql);
        
        $md5Pass = (!empty($rs)) ? $rs[0]['password'] : '';
          
        $token = md5($userkey . $md5Pass);
        
        // pake UTC  
        $temp = gmdate('U');
        $md5Hash = gmdate("Y/m/d H:i",$temp) .$token. SALT;
        
        // last token 
        // harusnya aman, utk sepersekian detik
        $tempLast =  gmdate('U',strtotime("-1 minute")); 
        $md5HashLastToken = gmdate("Y/m/d H:i",$tempLast) .$token. SALT;
         
//		$this->setLog('token 1 '. md5($md5Hash),true);
//		$this->setLog('token 2 '. md5($md5HashLastToken),true);

		if (!$includeLastToken){ 
            return md5($md5Hash);
        }else{
            return array(md5($md5Hash),md5($md5HashLastToken));
        }
 
    }
    
    function getUserAuthOTP($userName,$password){
        // ini dipake dari web, yg blm ad user / passwordnya 
        $result = $this->adminLogin($userName,$password);
        if(!$result['valid'])   
            return array();
         
        return array('userkey'=> $result['data']['pkey'], 'token' => $this->getUserOTP($result['data']['pkey']));
        
    }
    
    function checkAPIGETAuth($auth, $userkey = ''){ 
         
        //sementara
        if (empty($auth) || empty($userkey)) die; 
        
        //$usertoken =  (!empty($userkey)) ? $this->getUserOTP($userkey,true) : ''; 
        
        // kalo kirim userkey, pake OTP 
        $arrAppAuth = array(); 
        if(!empty($userkey)){
            $usertoken = $this->getUserOTP($userkey,true);
             
            foreach($usertoken as $token)
                array_push($arrAppAuth, hash_hmac('sha256', $token, $this->appsSecretKey)); 
        }else{ 
            // harusnya gk kepake lg kalo $userkey mandatory
            array_push($arrAppAuth, hash_hmac('sha256', '', $this->appsSecretKey)); 
        }
        
         
        return in_array($auth, $appAuth) ? true : false;
        
    }
    
    function checkAPIAuth($header,$fileContent, $userkey = ''){
        // userkey kosong dipake utk cek token sistem sepertinya
        
        $licenseCon = $this->masterConn(); 
        
        $PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 

        $url = $PROTOCOL .'://'. $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];    
           
        $sql = 'select secretkey from customer_company where name = '. $licenseCon->paramString(DOMAIN_NAME) .' and statuskey = 2';  
        $rsClient = $licenseCon->doQuery($sql);  
        $secretkey = $rsClient[0]['secretkey'];
        
        
        // new algo
        $arrAppAuth = array(); 
        $arrPayload = array(); 
        if(!empty($userkey)){
            $usertoken = $this->getUserOTP($userkey,true);
             
            foreach($usertoken as $token)
                array_push($arrPayload, $url . '|' . $fileContent . $token); 
        }else{  
            array_push($arrPayload,  $url . '|' . $fileContent . ''); // empty token
        }
        
        $arrAppAuth = array();
        $arrAuth = array();
        
        foreach($arrPayload as $payload){
            array_push($arrAppAuth, hash_hmac('sha256',$payload, $this->appsSecretKey));  // app
            array_push($arrAuth, hash_hmac('sha256',$payload, $secretkey));  // web
        }
        
        $licenseCon = null;

        $authorization =
        $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION']
        ?? null;
        
        if(in_array($authorization, $arrAppAuth))
            return true;
        else
            return (in_array($authorization, $arrAuth)) ? true : false;
            
        // kalo kirim userkey, pake OTP
       /* $usertoken =  (!empty($userkey)) ? $this->getUserOTP($userkey) : '';
        
        $payload =  $url . '|' . $fileContent . $usertoken;
        
        $appAuth =  hash_hmac('sha256',$payload, $this->appsSecretKey);  // app
        $auth =     hash_hmac('sha256',$payload, $secretkey); // web
        
        $licenseCon = null;
        
        if($appAuth == $authorization)
            return true;
        else
            return ($auth <> $authorization) ? false : true;*/
    }
    
    
    function checkAPIAuthV2($header, $userkey = ''){ 
        // userkey kosong dipake utk cek token sistem sepertinya
        
        $licenseCon = $this->masterConn(); 
        
        $PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 

        $url = $PROTOCOL .'://'. $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];    
           
        $sql = 'select secretkey from customer_company where name = '. $licenseCon->paramString(DOMAIN_NAME) .' and statuskey = 2';  
        $rsClient = $licenseCon->doQuery($sql);  
        $secretkey = $rsClient[0]['secretkey'];
        
        
        // new algo
        $arrAppAuth = array(); 
        $arrPayload = array(); 
        if(!empty($userkey)){ 
            $usertoken = $this->getUserOTP($userkey,true);
             
            foreach($usertoken as $token)
                array_push($arrPayload, array(
                                            'url' => $url,
                                            'userToken' => $token
                                        ) 
                          ); 
        }else{   
            array_push($arrPayload,  array(
                                            'url' => $url,
                                            'userToken' => ''
                                        )  ); 
        }
        
        $arrAppAuth = array();
        $arrAuth = array();
        
        foreach($arrPayload as $payload){ 
            array_push($arrAppAuth, hash_hmac('sha256',$payload['url'], $this->appsSecretKey).'.'.$payload['userToken']);  // app
            array_push($arrAuth, hash_hmac('sha256',$payload['url'], $secretkey).'.'.$payload['userToken']);  // web 
        }
        
        $licenseCon = null;

        $authorization =
        $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION']
        ?? null;

        if(in_array($authorization, $arrAppAuth))
            return true;
        else
            return (in_array($authorization, $arrAuth)) ? true : false;
    }
	
	
    function checkAPIAuthV3($header, $userkey, $accountType = 0){ 
        // userkey kosong dipake utk cek token sistem sepertinya
        
        $PROTOCOL = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http'; 

        $url = $PROTOCOL .'://'. $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];    
           
		// kalo ad license file, artinya standalone, cek secretekey dr settingan saja
		$offLineLicenseFile = DOC_ROOT.'license.php'; 
        if(is_file($offLineLicenseFile)){
			$secretkey = $this->loadSetting('minervaSecretKey');
		}else{
        	$licenseCon = $this->masterConn(); 
			$sql = 'select secretkey from customer_company where name = '. $licenseCon->paramString(DOMAIN_NAME) .' and statuskey = 2';  
			$rsClient = $licenseCon->doQuery($sql);  
			$secretkey = $rsClient[0]['secretkey'];
			$licenseCon = null;
		}
		
        
        // new algo 
        $arrPayload = array(); 
//        if(!empty($userkey)){ 
//            $usertoken = $this->getUserOTP($userkey,true);
//             
//            foreach($usertoken as $token)
//                array_push($arrPayload, array(
//                                            'url' => $url,
//                                            'userToken' => $token
//                                        ) 
//                          ); 
//        }else{   
//            array_push($arrPayload,  array(
//                                            'url' => $url,
//                                            'userToken' => ''
//                                        )  ); 
//        }
		
		// WAJIB AD USERKEY, bedakan dengan mobile apps
        // mobile app jg skrg pake otp
        // $arrAppAuth harusnya udah gk kepake kedepannya, deprecated
		$usertoken = $this->getUserOTP($userkey,true,$accountType); 
         
		foreach($usertoken as $token)
			array_push($arrPayload, array( 'url' => $url, 'userToken' => $token ) ); 

         
        $arrAuth = array();
		
        foreach($arrPayload as $payload){  
            array_push($arrAuth, hash_hmac('sha256',$payload['url'], $this->appsSecretKey).'.'.$userkey.'.'.$payload['userToken']);  // app
            array_push($arrAuth, hash_hmac('sha256',$payload['url'], $secretkey).'.'.$userkey.'.'.$payload['userToken']);  // web
        }
         
		$authorization =
        $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_REDIRECT_HTTP_AUTHORIZATION']
        ?? null;
         
        return (in_array($authorization, $arrAuth)) ? true : false;
        
    }
    
    
    function hasActiveMarketplace(){
        $sql = 'select pkey from marketplace where statuskey  = 1';
        $rs = $this->oDbCon->doQuery($sql);	
        
        return (empty($rs)) ? false : true;
    }
    
    function getRelatedDataForCashBankReport($pkey){
        $arrReturn = array();
        
        $sql = 'select 
                    '. $this->tableName.'.pkey, 
                    '. $this->tableName.'.code as refcode 
                from 
                    '. $this->tableName.' 
                where 
                    pkey in ('.$this->oDbCon->paramString($pkey,',').') ';
        
        $rs = $this->oDbCon->doQuery($sql); 
        $rs = array_column($rs, null,'pkey');
          
        return $rs;
    }
    
    
    function createFieldCache($arr, $delimiter = '*'){
        foreach($arr as $key=>$row){
            if(empty($row))
                unset($arr[$key]);
        }
         
        return $delimiter.implode($delimiter,$arr).$delimiter;
    }
    
    function includeClassDependencies($classFile, $createObject = true){
        if (!is_array($classFile)) $classFile = array($classFile); 
        foreach($classFile as $file){ 
            $filePath = DOC_ROOT. 'include/'.CLASS_VERSION.'/'.$file;
            if(is_file($filePath))
                require_once $filePath;
            
        }
    }
  
    function getReportSettings($tablekey, $userkey=''){
        
        if (empty($userkey))
            $userkey = $this->userkey;
        
        $sql = 'select value from report_settings 
                where reftabletpye = '. $this->oDbCon->paramString($tablekey). ' and 
                userkey in (0,' . $this->oDbCon->paramString($userkey).')';
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs; 
        
    }
    
    function getTransactionDescription($rs,$userkey = ''){ 
        return array();
    }
    
    function isJSON($string){
       return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

	function getDiscountValue($price, $discount,$discountType){
		  
		if ($discount != 0 && $discountType == 2)
			$discount = $discount/100 * $price;
		
		return $discount;
	}
	
    
    function getInvoiceRoundedTax($val, $roundType = 0){ 
    
    	switch($roundType) { 
                case 1:  return $val; break;
                case 2:  return ceil($val); break;
                case 3:  return floor($val); break; 
                default: return  round($val); break; // agar sama seperti Baseclass
        }
    
}
    
    function recalculateTaxAndValue(&$beforeTaxTotal,&$grandTotal,&$taxValue, $taxPercentage,$isPriceIncludeTax=true,$roundType=''){
       
        
        if($taxPercentage <=0 ) {
            $beforeTaxTotal = $grandTotal;
            $taxValue = 0;
            return;
        }
          
        if($beforeTaxTotal == 0)
            $beforeTaxTotal = $grandTotal;
            
        if ($isPriceIncludeTax == false) {
            
            $taxValue = $beforeTaxTotal * $taxPercentage / 100 ;
            
            switch ($roundType) {
                    case 1 :  break;
                    case 2 :  $taxValue =  ceil($taxValue) ; break;
                    case 3 :  $taxValue =  floor($taxValue) ; break;
                    default :  $taxValue = round($taxValue); break;
            }
           
            $grandTotal += $taxValue;
        }else{
                                        
            $taxValue =  ($taxPercentage/(100 + $taxPercentage)) * $grandTotal;   
            
            switch ($roundType) {
                    case 1 :  break;
                    case 2 :  $taxValue =  ceil($taxValue) ; break;
                    case 3 :  $taxValue =  floor($taxValue) ; break;
                    default :  $taxValue = round($taxValue); break;
            }
                                        
                                        
            $beforeTaxTotal = $grandTotal - $taxValue ;
        } 
        
    }
    
    function insertReportColumns($sliceIndex,$arrDataStructure,$arrAddStructure, $twig,&$arrTwigVar,  &$arrHeaderTemplate){
        
        $arrDataStructure = array_slice($arrDataStructure, 0, $sliceIndex, true) +  $arrAddStructure + array_slice($arrDataStructure, $sliceIndex, count($arrDataStructure)-$sliceIndex, true);

        /* overwrite data structure */ 
        $arrHeaderTemplate['dataStructure'] = $arrDataStructure; 

        $arrTemplate = array();
        array_push($arrTemplate, $arrHeaderTemplate);

        $arrTwigVar['arrTemplate'] =  $arrHeaderTemplate; 
        //$tableHeader = $twig->render('template-header.html', $arrTwigVar); // diluar saja, karena tetep harus direfresh ulang
        /* end of overwrite data structure */ 
        
           
        return array(
            'tableTemplate' => $arrTemplate,
            //'tableHeader' => $tableHeader
        );
    }
    

    function calculateTimeline($rsHistory,$arrOpt=array()){ 
 
        //$rsHistory per transaksi

        $arrReturn = array();

        $total = count($rsHistory);
 
        for($i=0;$i<$total;$i++){
            $statuskey = $rsHistory[$i]['actionkey'];

            if($statuskey == 11) $statuskey = 1; // kalo add, anggap menunggu

            // hitung dr status skrg ke status selanjutnya berapa lama
            // ad kemungkinan distatus tertentu bisa edit jg

            if (!isset($arrReturn[$statuskey])) $arrReturn[$statuskey] = 0;

            $dateFrom =  $this->formatDBDate($rsHistory[$i]['createdon'], 'd / m / Y H:i:s');

            // kalo bkn status terakhir
            $dateTo = (isset($rsHistory[$i+1])) ? $this->formatDBDate($rsHistory[$i+1]['createdon'],'d / m / Y H:i:s')  : date('d / m / Y H:i:s');

            $dateDiff = $this->dateDiff($dateFrom,$dateTo); 
            $arrReturn[$statuskey] += $dateDiff; // dlm seconds 
        }
 
        $interval = 86400;
        if(isset($arrOpt['interval'])){
            
            switch($arrOpt['interval']){
                case 'h' :  $interval = 3600;
                            break;
                default :  $interval = 86400;
                            break;
            }
        }

        
        foreach($arrReturn as $statuskey=>$row){ 
                $arrReturn[$statuskey] /= $interval;
        } 

        return $arrReturn;
    }
      
    function isActiveModule($modulename){
        $sql = 'select 
                    user_security_object.pkey, 
                    LOWER(security_object.modulecode) as modulecode
                from  
                    security_object,user_security_object 
                where  
                     user_security_object.statuskey = 1 and
                     user_security_object.security_object_key = security_object.pkey  and
                     security_object.modulecode in('.$this->oDbCon->paramString($modulename,',').')' ;
		
        $rs =  $this->oDbCon->doQuery($sql);
		
		if(!is_array($modulename)){ 
        	return (empty($rs)) ? false : true; 
		}else{
			$arrModuleCode = array_column($rs,'modulecode'); 
			$returnArr = array();
			
			foreach($modulename as $row){
				$row = strtolower($row); 
				$returnArr[$row] = (in_array($row,$arrModuleCode)) ? true : false;
			}
			
			return $returnArr;
		}
			
    }
    
    function getDefaultEmailVariable(){
        $arrTemplate = array();
        
	 	$companyName = $this->loadSetting('companyName');
	 	$companyEmailLogo = $this->loadSetting('emailLogo');
         
        $arrTemplate['COMPANY_NAME'] = $companyName;
        $arrTemplate['HTTP_HOST'] = HTTP_HOST;
        $arrTemplate['TEMPLATE_IMG_PATH'] = $this->templateImgPath;
        $arrTemplate['COMPANY_NAME'] = $companyName;
        $arrTemplate['EMAIL_LOGO'] = $companyEmailLogo;
		
		// kalo kirim auto email dr cron, gk ada fungsi getPHPThumbHash
        $arrTemplate['EMAIL_LOGO_HASH'] = getPHPThumbHash($companyEmailLogo);
        
        return $arrTemplate;
    }
     
    function grabImage($url,$saveto){
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($saveto)){
            unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }
    
	function getFrontEndTax($subtotal=0) { 
		$taxType = $this->loadSetting('frontEndTax');
		$taxType = json_decode($taxType,true);
        
		$includeTax = $taxType['include'];
		$taxPercentage = $taxType['percentage'];
		
		// gk pake tax 
	 	if($includeTax == 1) $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $subtotal;   
	    else if($includeTax == 0) $taxValue = $subtotal * $taxPercentage / 100;
		
		return array(
			'taxPercentage' => $taxPercentage,
			'taxValue' => $taxValue,
			'isPriceIncludeTax' => ($includeTax == 1) ? 1 : 0
		);
	}
		
	function toLocalDate($string,$langCode='', $fullMonth=true){
		 
        // kalo en di skip aj
        if(empty($langCode))
            $langCode = (isset($_SESSION['lang'])) ?  $_SESSION['lang'] : $this->langCode;
            
            
        if ($langCode != 'id')  return $string;
              
		$string = str_replace(array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'),$string); 
		
		if($fullMonth)
			$string = str_replace(array('January','February','March','April','May','June','July','August','September','October','November','December'),array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'),$string); 
		else
			$string = str_replace(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'),array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sept','Okt','Nov','Des'),$string); 
		
		return $string;
	}
	
    function formatPhoneNumber($string){
		
		$string = str_replace(array(' ' ,'-'),array('',''),$string);
		$string = strtolower($string); 
		return $string;
	} 
	
    
	function formatWA($string,$phoneCode='+62'){
		
		$string = $this->formatPhoneNumber($string); 
		$string = ltrim($string, "0");
		$string = ltrim($string, "+62");
		$string = ltrim($string, "62");
		$string = ltrim($string, "+");
		$string = $phoneCode.$string;
        
		return $string;
	} 
	
	function sendWA($phone,$content, $scheduler = false){
		// tembak dulu
		$merchant = 'woowa';
		
		switch(strtolower($merchant)){
			case 'woowa' :   $this->includeClassDependencies('WooWA.class.php');
							 $wooWA = new WooWA();
						     $wooWA->sendAsync($phone,$content,$scheduler);
						 break; 
		}

	}
	
    //API
    function manipulateDataGetAPI($rs){ return $rs; }
  /*  function removeArrParamAtIndex(&$arrParam, $index){
         $arrKeys = array_keys($arrParam);
         $this->setLog(json_encode($arrKeys));
        
         for ($i=0;$i<count($arrKeys);$i++) { 
             $indexName = $arrKeys[$i];
             $this->setLog($indexName .' => ' . $index);
             $arrParam[$indexName][$index] = 0;
             //unset($arrParam[$indexName][$index]); 
         }
    }*/
	
	


	/**
	*    mknatsort() - Multi-Key Natural Sort for associative arrays
	*
	*    Uses the usort() function to perform a natural sort on a multi-dimensional
	*    array on multiple keys. Optionally specifying the sort order for each key
	*    and/or ignoring the case for each key value.
	*
	*    @param array $data_array The array to be sorted.
	*    @param mixed $keys The list of keys to be sorted by. This may be a single
	*        key or an array of keys
	*    @param boolean $reverse Specify whether or not to reverse the sort order. If
	*        there are multiple keys then $reverse may be an array of booleans - one
	*        per key.
	*    @param boolean $ignorecase Specify whether or not to ignore the case when
	*        comparing key values.reverse the sort order. If there are multiple keys
	*        then $ignorecase may be an array of booleans - one per key.
	*/
	function mknatsort ( &$data_array, $keys, $reverse=false, $ignorecase=false ) {
		// make sure $keys is an array
		if (!is_array($keys)) $keys = array($keys);
		usort($data_array, $this->sortcompare($keys, $reverse, $ignorecase) );
	}

	function sortcompare($keys, $reverse=false, $ignorecase=false) {
		return function ($a, $b) use ($keys, $reverse, $ignorecase) {
			$cnt=0;
			// check each key in the order specified
			foreach ( $keys as $key ) {
				// check the value for ignorecase and do natural compare accordingly
				$ignore = is_array($ignorecase) ? $ignorecase[$cnt] : $ignorecase;
				$result = $ignore ? strnatcasecmp ($a[$key], $b[$key]) : strnatcmp($a[$key], $b[$key]);
				// check the value for reverse and reverse the sort order accordingly
				$revcmp = is_array($reverse) ? $reverse[$cnt] : $reverse;
				$result = $revcmp ? ($result * -1) : $result;
				// the first key that results in a non-zero comparison determines
				// the order of the elements
				if ( $result != 0 ) break;
				$cnt++;
			}
			return $result;
		};
	} // end sortcompare()
	
    function getPersonalizedFiles($fileName,$ext=''){     
        $ext = (empty($ext)) ? 'php' : ''; 
        $ext = (!empty($ext)) ? '.'.$ext : '';
        
        $path = 'admin/'.$fileName.$ext;
        $docPersonalizedFile = PERSONALIZED_DOC_PATH.$path;   
        $urlPersonalizedFile = PERSONALIZED_URL_PATH.$path;
		
        return (is_file($docPersonalizedFile)) ? $urlPersonalizedFile : $fileName; 
    }
 
	function getTMSPartner(){ 
		$sql = ' select * from  partner_settings where typekey = 1';
		$rs = $this->oDbCon->doQuery($sql); 
		
		return (!empty($rs)) ? $rs[0] : array(); 
	}
	 
	function executeCURL($url,$payload='',$opt=array()){
		
		$method = (isset($opt['method']) && !empty($opt['method'])) ? $opt['method'] : 'GET';
			
		$header = array(
			'Content-Type: application/json', 
			//'Content-Length: ' . strlen($payload),
			//'Authorization: Basic '.$auth
		);
		
		if (isset($opt['auth']) && !empty($opt['auth'])) 
			array_push($header, 'Authorization: '. $opt['auth']);
		
		
		$connection = curl_init(); 
		curl_setopt($connection, CURLOPT_URL, $url); 
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
		
		curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method); 
		curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 

		// sementara pasti terimanya JSON,
		// ketika terima kita sudah handling, bisa terima json / build_query 
		if(!empty($payload))  
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($payload)); 
     
		$response = curl_exec($connection);
		curl_close($connection); 
		return  $response; 
	}
	
	
	function getPartnerOTP($partnerUrl,$userkey){
		$url = $partnerUrl.'/api/v3/otp/userkey='.$userkey;  
		return $this->executeCURL($url); 
	}
	
	function executeAPIPartner($partnerAccount,$url,$method='GET',$payload='', $updateTransPartnerId = true){
		     	
		$userkey =  $partnerAccount['defaultuserkey'];
        
        // getUserOTP, menyamakan dengan apps, 
        // asumsi hanya user yg punya akses sudah di validasi dihalaman import
        $tokenResult = $this->getPartnerOTP($partnerAccount['partnerurl'], $userkey); //  (!empty($userkey)) ? $this->getUserOTP($userkey) : '';
        $tokenResult = json_decode($tokenResult,true);
		$usertoken = $tokenResult['data'];
			
        // header AUTH    
        $secretkey = $partnerAccount['partnersecretkey'] ;
		
		$auth = hash_hmac('sha256',  $url,$secretkey).'.'.$userkey.'.'.$usertoken;
		
		$result = $this->executeCURL($url,$payload, array('auth' => $auth, 'method' => $method)); 
		$result = json_decode($result,true);  
		 
		// add ke partner id
		if(($method == 'POST' || $method == 'PUT') && $updateTransPartnerId){

			$partnerId = $result['success_data'][0]['code'];
			
			try{  

				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 

				//$payload['request_id'] -> $pkey
				$sql = 'update '. $this->tableName.' set partnerid = '.$this->oDbCon->paramString($partnerId).' where pkey = ' . $this->oDbCon->paramString($payload['request_id']);
				$this->oDbCon->execute($sql);

				$this->oDbCon->endTrans(); 

			}catch(Exception $e){
				$this->oDbCon->rollback();  
			}

		}
		
		//$this->setLog($result,true);
		return $result;
	}
	
	function getPartnerId($obj,$pkey){
		$rs = $obj->searchDataRow(array($obj->tableName.'.pkey', $obj->tableName.'.partnerid'), 
										    ' and '.$obj->tableName.'.pkey = '.$this->oDbCon->paramString($pkey));
		
		return (!empty($rs)) ? $rs[0]['partnerid'] : '';
	
	}
	
	function checksumMD5($code,$checksum){
		// sementara harus dr code dan kolom checksum
		
		$sql = 'select checksum from '.$this->tableName.' where code = '. $this->oDbCon->paramString($code);
		$rs = $this->oDbCon->doQuery($sql);
		
		if (empty($rs)) return false;
		
		return ($rs[0]['checksum'] == $checksum) ? true : false;
		
	}
  
	function getPaymentGatewayInvoice($arr){
		$partner =  strtolower($this->loadSetting('PaymentGateway'));
		if(empty($partner)) return;
		
		$partner =  strtolower($partner);
		$response = array();
		
		switch($partner){
			case 'xendit':  $this->includeClassDependencies(array('Xendit.class.php'));
							$xendit = new Xendit();
							$response = $xendit->getInvoice($arr);
				
				default : break;
		}
		
		// standart response paling tidak harus ada index, dibawah, kalo gk ada, dinormalize
		
		// invoice_url : 
		
		return json_decode($response,true); // idealnya return dr API dalam bentuk json, jd harus didecode dulu
	}
	
	function generateHourSelectBox($interval=30){
		$multiply = 60 / $interval;
		$total = 24 * $multiply;
		
		$hour = 0;
		$arr = array();
		for($i=0;$i<$total;$i++){
			
			$label = ($hour < 10) ? '0'.$hour: $hour ;
			$label .= ($i%2 <> 0) ? ':30': ':00';
			
			if( $i%2 != 0 ) $hour++;
			
			$arr[$label] = $label;
		}
		
		return $arr;
	}
    
    function generateYearSelectBox($lastYear = '', $totalYear =  10, $reverse = false, $groupYear=0){
        if($lastYear == '') $lastYear = date('Y');
        
        
        if($groupYear <= 1){
            $startYear = $lastYear - $totalYear; 
            for($i=$startYear;$i<=$lastYear;$i++) 
                $arr[$i] = $i;  
        }else{
            $ctr = 0;
            $startYear = $lastYear - (($totalYear+1)*$groupYear) - 1;
            
            $currYear = $startYear;
            while($ctr < $totalYear){
                
                $arr[$currYear.'-'.($currYear+$groupYear)] = $currYear.'-'.($currYear+$groupYear);  
                 
                $currYear += $groupYear;
                $currYear++; 
                $ctr++;
            }
        }
        
		return ($reverse) ? array_reverse($arr,true) : $arr;
    }
    
   function generateMonthSelectBox($reverse = false){
    
		for($i=1;$i<=12;$i++) 
			$arr[$i] =  date('F', mktime(0, 0, 0, $i, 10)); 
        
        $arr = $this->toLocalDate($arr);
		return ($reverse) ? array_reverse($arr,true) : $arr;
    }
    
    
	
	function calculateAge($dateOfBirth){ 
		// $dateOfBirth in Y-m-d 
        $diff = date_diff(date_create($dateOfBirth), date_create(date('Y-m-d'))); 
        $age = $diff->format('%y'); 
        return $age;
        
    }
 
	function createReportCriteria(&$criteria,&$arrFilterInformation,$criteriaArr){
		  
		if(!isset($_POST)) return; 
		
		foreach($criteriaArr as $criteriaRow){
			if(empty($criteriaRow['postVariable'])) continue;
			
			$postVariable = $criteriaRow['postVariable'];
			$fieldName = ( isset($criteriaRow['fieldName']) ) ? $criteriaRow['fieldName'] : '';
			$label =  ( isset($criteriaRow['label']) ) ? $criteriaRow['label'] : ''; 
			$useArrayKey = (isset ($criteriaRow['useArrayKey'])) ? $criteriaRow['useArrayKey'] : array();
				
			// utk yg biasa
			if(!is_array($postVariable) && empty($_POST[$postVariable])) continue;
			
			if(!isset($criteriaRow['type'])) $criteriaRow['type'] = 'general';
				
			$filterInformation = '';
			
			switch($criteriaRow['type']){
				
				case 'daterange' : 
					
					$criteria .= ' and '.$fieldName.' between '.$this->oDbCon->paramDate( $_POST[$postVariable[0]],' / ').' AND '.$this->oDbCon->paramDate($_POST[$postVariable[1]],' / ', 'Y-m-d 23:59'); 
					$filterInformation = $_POST[$postVariable[0]] .' - '.  $_POST[$postVariable[1]];
					break;
					
				
				case 'status' : 
					
					$key = implode(',', $this->oDbCon->paramString($_POST[$postVariable]));   
				    $criteria .=  ' AND  '.$this->tableName.'.statuskey in('.$key.')';  

        			$rs =  $this->getStatusById($key);
	 				$filterInformation =  implode(', ',array_column($rs,'status')); 
					$label = $this->lang['status'];
					
					break;
					
			    case 'criteria' :  
				    $criteria .=  $criteriaRow['criteria'];   
				    $filterInformation =  $criteriaRow['filter'];   
					break;
					
				default :  
//					$key = implode(',', $this->oDbCon->paramString($_POST[$postVariable]));    

					// kalo tipenya daterange 
					if(!empty($useArrayKey)){
						$obj = $useArrayKey['obj'];

						$criteria .= ' AND '.$fieldName.' in('.$this->oDbCon->paramString($_POST[$postVariable],',').')';  
						$rs = $obj->searchDataRow( array('name'),  ' and '.$obj->tableName.'.pkey in ('.$this->oDbCon->paramString($_POST[$postVariable],',').')'); 
						$filterInformation = implode(', ',array_column($rs,'name'));   
					} else{
						$criteria .= ' AND '.$fieldName.' LIKE ('.$this->oDbCon->paramString('%'.$_POST[$postVariable].'%').') ';
						$filterInformation = $_POST[$postVariable];
					}  
					
					break; 
			}
			
			
			array_push($arrFilterInformation,array("label" => $label, 'filter' => $filterInformation ));  
		}
		
	}
	
	 function setActivityTransactionLogDetail($pkey, $objRef, $reftransactionkey){
		 // utk activity log
		 
		 // setActivityTransactionLogDetail(pkey, new MedicalRequestClaim(), pkeyRequest)
		 
		$reftablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
		$reftableObjkey = $this->getTableKeyAndObj($objRef->tableName,array('key'))['key']; 
		
        $criteria = ' and '. $this->transactionLog . '.refkey = ' . $this->oDbCon->paramString($pkey);
        $criteria .= ' and '. $this->transactionLog . '.tablekey = ' . $this->oDbCon->paramString($reftablekey);
		 
		$sql = 'select pkey from '. $this->transactionLog . ' where 1=1 '. $criteria. ' order by pkey desc limit 1';
		$rsTransactionLog = $this->oDbCon->doQuery($sql);
  
        $arrayToJs = array();
        try {
            if (!$this->oDbCon->startTrans()) 
                throw new Exception($this->errorMsg[100]);
		    
            $sql =  'insert into 
					'. $this->transactionLogDetail.' (
						tablekey,
						refkey ,
						reftransactionkey
					) values( 
						'.$this->oDbCon->paramString($reftableObjkey).',
						'.$this->oDbCon->paramString($rsTransactionLog[0]['pkey']).',
						'.$this->oDbCon->paramString($reftransactionkey).'
					)'; 

		    $this->oDbCon->execute($sql);

            $this->oDbCon->endTrans(); 
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }
        return $arrayToJs;
	}
	
	 function getActivityLog ($obj, $criteria = '') {
        $rs = $this->getActivityTransactionLog($obj, $criteria);
        $rsDetail = $this->getActivityTransactionLogDetail($rs[0]['refkey'], $rs[0]['tablekey']);
        $rs = array_merge($rs,$rsDetail);

        $this->mknatsort($rs, 'pkey', true ,true);
        $this->mknatsort($rs, 'createdon', true ,true);
        return $rs;
    }
	
    function getActivityTransactionLog($obj,$criteria = ''){
        
        $sql = 'select
                    ' . $this->transactionLog . '.*,
                    ' . $this->tableEmployee . '.name as employeename,
                    ' . $this->transactionLogAction . '.name as actionname,
                    ' . $obj->tableName . '.code
               from
                    ' . $this->transactionLog . '
                        left join '.$this->tableEmployee.'  on '.$this->transactionLog.'.createdby = '.$this->tableEmployee.'.pkey 
						left join '.$obj->tableName.'  on '.$this->transactionLog.'.refkey = '.$obj->tableName.'.pkey,
                        '. $this->transactionLogAction .'
               where
                       '.$this->transactionLog.'.actionkey = '.$this->transactionLogAction.'.pkey';
        
        $sql .= $criteria;
  
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }


    function getActivityTransactionLogDetail($pkey, $tablekey){
        $sql = 'select
                    ' . $this->transactionLogDetail . '.tablekey,
                    ' . $this->transactionLogDetail . '.refkey,
                    ' . $this->transactionLogDetail . '.reftransactionkey,
                    ' . $this->transactionLog . '.*,
                    ' . $this->tableEmployee . '.name as employeename,
                    ' . $this->transactionLogAction . '.name as actionname
               from
                    ' . $this->transactionLogDetail . '
                        left join '.$this->transactionLog.'  on '.$this->transactionLogDetail.'.refkey = '.$this->transactionLog.'.pkey
                        left join '.$this->transactionLogAction.'  on '.$this->transactionLog.'.actionkey = '.$this->transactionLogAction.'.pkey
                        left join '.$this->tableEmployee.'  on '.$this->transactionLog.'.createdby = '.$this->tableEmployee.'.pkey
               where
                '.$this->transactionLogDetail.'.tablekey = '. $this->oDbCon->paramString($tablekey).' and 
               ' . $this->transactionLogDetail . '.reftransactionkey in (' . $this->oDbCon->paramString($pkey, ',') . ')';

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }
	
	 function getCompanySubType(){
   
        $sql = 'select subcategorykey from '.$this->tablePlanType.'';
        $rs = $this->oDbCon->doQuery($sql); 
        $arrCategoryKey = explode(',',$rs[0]['subcategorykey']);

        $arrType = array();
        
        for($i=0;$i<count($arrCategoryKey);$i++){ 
			
			// nama table sementara tembak manual dulu
			
			$label = ''; 
			$invoiceTableName = '';
			switch($arrCategoryKey[$i]){
				case  COMPANY_TYPE['trucking'] :   $label = 'TMS'; 
												   $invoiceTableName = 'trucking_service_order_invoice_header'; 
													break;
				case  COMPANY_TYPE['forwarding'] :  $label = 'FMS'; 
												    $invoiceTableName = 'emkl_order_invoice_header'; 
													break;
			}
           
			array_push($arrType, array(
											'label' => $label,
											'categorykey' => $arrCategoryKey[$i],
											'invoicetabletypekey' =>  $this->getTableKeyAndObj($invoiceTableName,array('key'))['key']
										)
					   );
		} 
        
        return $arrType; 
        
    }
    
    function getJournal($tabletypekey,$pkey){
        
        if(!$this->isActiveModule('GeneralJournal')) return ;
        
        $sql = 'select 
                        general_journal_header.pkey,general_journal_header.code ,general_journal_header.trdate,
                        general_journal_detail.debit, general_journal_detail.credit,general_journal_detail.trdesc,general_journal_detail.refcode,
                        chart_of_account.code as coacode,chart_of_account.name as coaname
                from 
                        general_journal_header, general_journal_detail, chart_of_account
                where 
                        general_journal_header.pkey = general_journal_detail.refkey and general_journal_header.statuskey in (2,3)  and
                        general_journal_detail.coakey = chart_of_account.pkey and
                        general_journal_header.reftabletype = '.$this->oDbCon->paramString($tabletypekey).' and
                        general_journal_header.refkey = '.$this->oDbCon->paramString($pkey).'
            ';
         
        return $this->oDbCon->doQuery($sql);
    }
    
    function updateThemeSettings($settingkey, $value){
        // cek dulu sudah ad blm, klo blm ad add
         
        if(empty($settingkey)) return;
        
        $sql = 'select pkey,value,settingkey from '.$this->tableThemeSetting.'
                where userkey = '.$this->oDbCon->paramString($this->userkey).' and settingkey = ' .$this->oDbCon->paramString($settingkey);
        $rs  = $this->oDbCon->doQuery($sql);
         
        $arrSettings = array_column($rs,'value','settingkey');
        
          try {
            if (!$this->oDbCon->startTrans()) 
                throw new Exception($this->errorMsg[100]);
		    

                if(is_array($value))
                    $value = '\''.addslashes(json_encode($value)).'\'';
                else
                    $value = $this->oDbCon->paramString($value);

                if(!isset($arrSettings[$settingkey])){
                    $sql = 'insert into  '.$this->tableThemeSetting.' (settingkey,userkey,value) 
                            values (
                            ' .$this->oDbCon->paramString($settingkey).',
                            ' .$this->oDbCon->paramString($this->userkey).', 
                            ' .$value.'
                            ) ';
                }else{
                     $sql = 'update  '.$this->tableThemeSetting.' 
                            set  value = ' .$value.'
                            where
                                settingkey = ' .$this->oDbCon->paramString($settingkey).' and
                                userkey =  ' .$this->oDbCon->paramString($this->userkey);

                }

                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans(); 
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }
        
        
        
    }
    
//    function getThemeSettings($settingkey = ''){
//        $sql = 'select pkey,settingkey,userkey,value from '.$this->tableThemeSetting.' where userkey = ' .$this->oDbCon->paramString($this->userkey);
//        
//        if(!empty($settingkey))
//            $sql .= ' and  settingkey = ' .$this->oDbCon->paramString($settingkey);
//            
//        return $this->oDbCon->doQuery($sql);
//    }
//    
    function validateLinkedData($obj, $opt = array()){
        
        $statuskey =  (isset($opt['statuskey'])) ? $opt['statuskey'] : array(2,3); 
        
        $negation = (isset($opt['negationStatus']) && $opt['negationStatus'] == true) ? ' not ' : '';
        
        $criteria =  ' and '.$obj->tableName.'.statuskey '.$negation.' in ('.$this->oDbCon->paramString($statuskey,',').')';
        
        if(isset($opt['linkedField']))
            $criteria .= ' and ' . $opt['linkedField']['field'] .' in ('.$this->oDbCon->paramString($opt['linkedField']['value'],',').')';
            
        //$criteria = (isset($opt['criteria'])) ? $opt['criteria'] : ' and '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$obj->tableName.'.statuskey '.$negation.' in ('.$this->oDbCon->paramString($statuskey,',').')' ;
		 
        $rs = $obj->searchDataRow( array($obj->tableName.'.pkey',$obj->tableName.'.code'),$criteria); 
        
        if(!empty($rs)) {

            $errorMsg = '';
            if(isset($opt['refCode'])) $errorMsg .= '<strong>'.$opt['refCode'].'</strong>. ';
            $errorMsg .=  $this->errorMsg[$opt['errorCode']] . ' ';

            // error header
            if(isset($opt['errorMsg'])) $errorMsg .= $opt['errorMsg'];
            
            // kalo ad detail
            if(isset($opt['errorDetailMsg'])) 
                $errorMsg .= '<br><b>'.implode( array_column($rs,'code'),', ') .'.</b> ' .$opt['errorDetailMsg'];
            
		 	$this->addErrorLog(false, $errorMsg ,true);
        }

    }
    
    function getGMT(){
        $return = array();
        for($i=-12;$i<=14;$i++) { 
            if ($i==0)
                $return[$i] = 'GMT';  
            else
                $return[$i] = 'GMT ' . ( ($i>0) ? '+' : '' ) . $i; 
        }
        
        return $return;
    } 
    
    function convertToLocalTimeZone($time,$timeZone,$convertToTimeZone){
        // $time dlm format mysql
         
        $interval = $convertToTimeZone - $timeZone ; 
        
        $date = new DateTime($time);
        
        if ($interval == 0) return $date->format('Y-m-d H:i'); 
            
        if($interval > 0)
            $date->add(new DateInterval('PT'.$interval.'H'));
        else
            $date->sub(new DateInterval('PT'.abs($interval).'H'));
        
        //$this->setLog($time. ' from '.$timeZone.' to '.$convertToTimeZone. ' == ' .$date->format('Y-m-d H:i') ,true);
        return $date->format('Y-m-d H:i'); 
    }
    
    
    function getAttachmentToPrint($id){
        return ;
    }
    
    
    function getIPLocalInformation($ipAddress=''){
        
            // Initialize cURL.
            $ch = curl_init();

            // Set the URL that you want to GET by using the CURLOPT_URL option.
            // tembak mati dulu api nya
            $url = 'https://ipgeolocation.abstractapi.com/v1/?api_key=1b371961168d4f5a909aa0d88a89f884';
            if(!empty($ipAddress))
                $url .= '&ip_address='.$ipAddress;
        
            curl_setopt($ch, CURLOPT_URL,$url);

            // Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            // Execute the request.
            $data = curl_exec($ch);

            // Close the cURL handle.
            curl_close($ch);

            // Print the data out onto the page.
            $data = json_decode($data,true);
        
            includeClass('Country.class.php');
            $country = new Country();
            $rsCountry = $country->searchDataRow( array($country->tableName.'.pkey',$country->tableName.'.code',$country->tableName.'.name',$country->tableName.'.phonecode') , 
                                                 ' and '.$country->tableName.'.code = ' . $this->oDbCon->paramString($data['country_code']) );
         
        
            $returnValue = array(
                    'ip' =>   $data['ip_address'],
                    'countrykey' =>   $rsCountry[0]['pkey'],
                    'countrycode' =>  $rsCountry[0]['code'],
                    'country' =>   $rsCountry[0]['name'],
                    'phonecode' => $rsCountry[0]['phonecode'],
                    'timezone' =>  array('gmt' => $data['timezone']['gmt_offset'] ) ,
            );
        
            return $returnValue;

    }
    
    function getLangTemplatePath($url,$useLang = false, $lang = ''){
        if(!$useLang) return $url;
         
        if (empty($lang)) {
            
            $lang = 'id';
            
            // kalo gk didefine lang dan gk login
            if (isset($_SESSION[$this->loginSession]) && !empty($_SESSION[$this->loginSession]['id'])){
                $sql = 'select lang.code as langcode from lang,customer where customer.langkey = lang.pkey and customer.pkey = ' . $this->oDbCon->paramString(base64_decode($_SESSION[$this->loginSession]['id']));
                $rs = $this->oDbCon->doQuery($sql);
                
                if (!empty($rs[0]['langcode'])) 
                    $lang = $rs[0]['langcode'];
            }elseif(!empty($_SESSION['lang'])){
                $lang = $_SESSION['lang'];
            }
            
        }
         
            
        $langPath = 'lang/'.$lang.'/'; 
        return (is_file($this->templateDocPath.$langPath.$url)) ? $langPath.$url : $url;
    }
    
    function resizeRatio($width,$height,$opt){
//        
//        $this->setLog($width,true);
//        $this->setLog($height,true);
//        $this->setLog($opt,true);
//        
        $type = $opt['type']; // 1=> size, 2=> ratio // sementara blm ad ratio
        $boundW = $opt['size']['w'];
        $boundH = $opt['size']['h'];
            
        $arrReturn = array('w'=>$width,'h'=>$height);
        
        if($type == 1){
            $diffW = ($width > $boundW) ? (($width-$boundW) / $width) * 100 : 0;
            $diffH = ($height > $boundH) ? (($height-$boundH) / $height) * 100 : 0; 
            $diffPercentage = ($diffW > $diffH) ? $diffW : $diffH; 
            $newW = $width - ( $diffPercentage/100*$width);
            $newH = $height - ( $diffPercentage/100*$height);
            
            $arrReturn = array('w'=>$newW,'h'=>$newH);
        }
        
        return $arrReturn;
        
    }
    
    function afterARStatusChanged($pkey){ 
        // utk saat ini baru dipake utk disposal invoice (BHUMAN)
        // pkey = pkey transaksi, bukan pkey AR
    }
    
//    function isDateDiffGreaterThan($dateFrom='',$dateTo='',$days = 0){
//        
//        if(empty($dateFrom)) $dateFrom = date('d / m / Y');
//        if(empty($dateTo)) $dateTo = date('d / m / Y');
//           
//        $dateDiffUntilExp = $this->dateDiff($dateFrom, $dateTo) / 86400;   
//        return ($dateDiffUntilExp < $days) ? 1 : 0 ;
//    }
    

    function utf8ize($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->utf8ize($v);
            }
        } else if (is_string ($d)) {
            return utf8_encode($d);
        }
        return $d;
    }
    
    function formatForMonthly($date){
        // format semetnara dd / mm / yyyy
        $arr = explode(' / ', $date);
        return '01 / '. $arr[1].' / '.$arr[2];
        
    }
    
    function formatForAnnualy($date){
        // format semetnara dd / mm / yyyy
        $arr = explode(' / ', $date);
        return '01 / 01 / '.$arr[2];
        
    }
    
    function getLang($lang){
        return (isset($this->lang[$lang])) ? $this->lang[$lang] : '';
    }
    
    function URLFilter($string){
        $string = strtolower(str_replace($this->arrSearch,$this->arrReplace,$string));
        
        // remove duplicate --
        $arrDuplicate = array('-');
        $chars = str_split($string);
        $lastChar = '';

        $newString = '';
        foreach ($chars as $char) {
            if(in_array($char, $arrDuplicate) && $char == $lastChar)
              continue;

            $newString .= $char;
            $lastChar = $char;
        }

        return $newString;
    }
	
	function isEmptyDate($dateString){
		$arrEmptyDate = array('0000-00-00',
							  '0000-00-00 00:00:00', 
							  '1970-01-01',  
							  '1970-01-01 00:00:00');
		
		return (!empty($dateString) && !in_array($dateString, $arrEmptyDate)) ? false : true;
	}
	
	function filenameSanitizer($unsafeFilename){

	  // our list of "unsafe characters", add/remove characters if necessary
	  $dangerousCharacters = array(" ", '"', "'", "&", "/", "\\", "?", "#");

	  // every forbidden character is replaced by an underscore
	  $safe_filename = str_replace($dangerousCharacters, '_', $unsafeFilename);

	  return $safe_filename;
	}
	
    
    function stitchDescriptionV2($opt){
        // support multilevel
        // pattern dalam JSON
        
        
		$arrAvailableField = $opt['field'];
        $arrAvailableFieldByGroup = $this->reindexDetailCollections($arrAvailableField,'tableName');     
        $arrAvailableFieldParam = array_column($arrAvailableField,'param');
          
        
        $fieldCol = array_column($arrAvailableField, 'param');
        
        
		$arrKey = (!is_array($opt['pkey'])) ? array($opt['pkey']) : $opt['pkey'];
        $pkey = array_unique(array_column($arrKey,'pkey'));
        
        // init
        $arrData = array();
        foreach($pkey as $row)
            $arrData[$row] = array();
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];  
		$rsSettings = $this->getReportSettings($tablekey);  
        
        if(empty($rsSettings)) return array(); 
        if(!$this->isJSON($rsSettings[0]['value'])) return array();
        
        $reportPattern = json_decode($rsSettings[0]['value'],true); 
        
        // preg_match dulu 
        $arrPregMatch = array();
        foreach($reportPattern as $key=>$patternRow){ 
             // cari adanya pattern apa saja
             preg_match_all('/\{\{(.*?)\}\}/', $patternRow, $matches);  
            
            $arrPregMatch[$key] = $matches[0];
        }

        
        // kumpulin dulu bagian header dan detail biar sekalian query 
        
        $arrGroup = array();
        foreach($arrAvailableFieldByGroup as $key=>$groupRow){
            if(!isset($arrGroup[$key])) $arrGroup[$key] = array();
            
            foreach($groupRow as $row){

                array_push($arrGroup[$key],array('param' => $row['param'],
                                                 'tableName' => (isset($row['tableReference'])) ? $row['tableReference']['tableName'] : $this->tableName,
                                                 'field' => (isset($row['tableReference'])) ? $row['tableReference']['field'] : $row['field'],
                                                 'joinkey' =>   (isset($row['tableReference'])) ? $row['tableReference']['refkey'] : '' // jgn diisi kalo ambil dari table utama. buat patokan perlu left join atau gk perlu
                                                ));
            }
            
        }
         
        
        foreach($arrGroup as $key=>$groupRow){
            
            $tableName = $key;
            
            // tembak mati dulu
            if ($tableName==$this->tableName){  
                $refField = 'pkey';  
            }else{ 
                 $refField = 'refkey';  
            }
            
            

              $arrSelectField = array();
              $arrLeftJoinCriteria = array();

             foreach($groupRow as $row){

                    array_push($arrSelectField, $row['field']. ' as ' .$row['param']);

                    // kalo gk ad join (hanya ambil dari field table utama), skip
                    if (!empty($row['joinkey']))
                     array_push($arrLeftJoinCriteria, 'left join '.$row['tableName'].' on '. $tableName.'.'.$row['joinkey'] .' = '.$row['tableName'].'.pkey' );
              }

              $sql = 'select '.$tableName.'.'.$refField.' ,'.implode(',', $arrSelectField).'
                            from '.$tableName.' ' .implode(' ', $arrLeftJoinCriteria).'  
                            where '.$tableName.'.'.$refField.' in ('.$this->oDbCon->paramString($pkey,',').')
                     '; 
              $rs = $this->oDbCon->doQuery($sql);
            
              // gk bisa pake + atau merge, hasilnya beda
              foreach($rs as $rsRow){
                   
                  
                  foreach($arrAvailableField  as  $paramRow){ 
                       
                          $field = $paramRow['param'];
                          $dataPkey = $rsRow[$refField];
                      
                          if (!isset($rsRow[$field])) continue;
                        
                          // semua jadiin arraym termasuk header
                          if(!isset($arrData[$dataPkey][$field])) $arrData[$dataPkey][$field] = array();
                          array_push($arrData[$dataPkey][$field], $rsRow[$field]);
                       
                  }
                  
              }
        }
         
        
        // start stitching
         
        $arrReturn = array();
        foreach($arrData as $pkey=>$row){
             
            $content = array();
            $arrSearch = array();
            $arrReplace = array();
             
            // di replace per baris index json
            
            
           $arrReplacement = array();
            
            foreach($arrAvailableFieldParam as $paramRow){
                  
                $value = (!empty($row[$paramRow])) ? $row[$paramRow] : array();
                
                if(empty($value)){  
                    array_push($arrReplacement, array('search' => '{{'.$paramRow.'}}', 'replace' => '' )); 
                } else {
                    foreach($value as $valueRow){
                        array_push($arrReplacement, array('search' => '{{'.$paramRow.'}}', 'replace' => $valueRow ));  
                    }
                }
                  
            }
            
            $arrReplacement = $this->reindexDetailCollections($arrReplacement,'search');    
                
            foreach($reportPattern as $key=>$patternRow){
                
                 // cari adanya pattern apa saja
//                 preg_match_all('/\{\{(.*?)\}\}/', $patternRow, $matches); 
                 
                 $matchesPattern =  $arrPregMatch[$key];
                 
                
                 $totalLines = 0;
                 foreach($matchesPattern as $matchRow) 
                     if (count($arrReplacement[$matchRow]) > $totalLines) $totalLines = count($arrReplacement[$matchRow]);
                 
                
                for($i=0;$i<$totalLines;$i++){
                    
                    $tempContent = $patternRow; 
                    foreach($matchesPattern as $matchRow) 
                        $tempContent = str_replace( $arrReplacement[$matchRow][$i]['search'] , $arrReplacement[$matchRow][$i]['replace'] ,$tempContent);
                    
                    if(!empty($tempContent))
                        array_push($content, $tempContent);
                    
                }
                  
            }
            
            
            $arrReturn[$pkey] = implode('<br>',$content);
            
        }
         
        return $arrReturn;
         
    }
    
    
	function stitchDescription($opt){
        // refField : utk join ke refkey nya GL. kalo header berarti pkey. kalo detail berarti refkey (pkeynya header)
		  
		$arrAvailableField = $opt['field'];
		$arrKey = $opt['pkey'];
//		$userkey = $opt['userkey']; // buat nanti kalo bisa beda2 per user 
//        if (empty($userkey))
//            $userkey = $this->userkey;
		
		
		
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];  
		$rsSettings = $this->getReportSettings($tablekey);  
        
        if(empty($rsSettings)) return array();
		
		
        $reportPattern = $rsSettings[0]['value']; 
		
		$pkey = array_unique(array_column($arrKey,'pkey'));
		
		$usedParam = array(); 
        foreach($arrAvailableField as $row) { 
             if(strpos($reportPattern, '{{'.$row['param'].'}}') !== false) 
                 array_push($usedParam,$row);  
		} 
          
        $queryselect = array();
        foreach($usedParam as $row) { 
			
			if (isset($row['tableDetail']) && !empty($row['tableDetail'])){
				$tableName =  $row['tableDetail']['tableName'];
				$fieldName =  $row['tableDetail']['field'];
			}elseif (isset($row['tableReference']) && !empty($row['tableReference'])){
				$tableName =  $row['tableReference']['tableName'];
				$fieldName =  $row['tableReference']['field'];
			}else{ 
				$tableName =  $this->tableName; 
				$fieldName =  $row['field'];
			}
			 
			if (!isset($queryselect[$tableName]))  $queryselect[$tableName] = array(); 
			
			$refField = '';
			$refkey = '';
			$tableType = 0;
            
            if(isset($row['tableDetail'])){
                $refField = $row['tableDetail']['refField']; // harusna wajib isi
				$refkey = (isset($row['tableDetail']['refkey'])) ? $row['tableDetail']['refkey'] : $tableName.'.refkey,'; // sebelumny gk ad, test dulu
                $tableType = 1;
            }else if(isset($row['tableReference'])){
                $refField = $row['tableReference']['refField'];
                $refkey = $row['tableReference']['refkey'];
                $tableType = 2;
            }
            
//			if(isset($row['tableDetail']['refField']) && !empty($row['tableDetail']['refField'])){
//				$refField = $row['tableDetail']['refField']; 
//				$refkey = (isset($row['tableDetail']['refkey']) && !empty($row['tableDetail']['refkey'])) ? $row['tableDetail']['refkey'] : $tableName.'.refkey,'; // sebelumny gk ad, test dulu
//				$tableType = 1;
//			}else if(isset($row['tableReference']['refField']) && !empty($row['tableReference']['refField'])){
//				$refField = $row['tableReference']['refField'];
//				$refkey = $row['tableReference']['refkey'];
//				$tableType = 2;
//			}
						array_push($queryselect[$tableName], array('criteria' => $fieldName .' as '. $row['code'],
													   'refField' => $refField,
													   'refkey' => $refkey,
													   'tableType' => $tableType,
                                                       'tableName' => (!empty($row['tableDetail']['tableName'])) ? $row['tableDetail']['tableName'] : array()     //diharapkan kedepan nya bisa rekursif kedetail level 3 dst, jd pake nama tableName jg
													  )
					 ); 
		}
             
        if (empty($queryselect))  return array();
             
			
		$returnArr = array();
		 
		foreach($queryselect as $tableName=>$row){
			    
			if ($tableName == $this->tableName){
				// kalo header
				$refField =  'pkey';
			}else{
				$refField = (isset($row[0]['refField']) && !empty($row[0]['refField'])) ? $row[0]['refField'] : 'refkey';
			}
	 		
			
			// refkey => utk select column yg digunakan utk direindex ke pkey header (dari sisi table lain)
			$refkeyField = (isset($row[0]['refkey']) && !empty($row[0]['refkey'])) ? $tableName.'.refkey as refkey,' : ''; // dipisah agar bisa dioverwrite jika ada yg bukan refkey
  
			if ($row[0]['tableType'] == 2){
					// tipe referensi ke table lain
					$sql = 'select '.$tableName.'.pkey,'.$this->tableName.'.pkey as refkey, '.implode(',', array_column($row,'criteria')).'
							from '.$tableName.','.$this->tableName.'
					 		where 
								'.$this->tableName.'.'.$row[0]['refkey'].' = '.$tableName.'.pkey and 
								'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').' ) 
					 	 
					'; 
			}else{
					// tipe 1 table atau table detail
                
                    // kalo ad join ke table lain, pake left join
//                    $arrJoinTable = array();
                
					$sql = 'select '.$tableName.'.pkey,'.$refkeyField.' '.implode(',', array_column($row,'criteria')).'
							from '.$tableName.' 
							where '.$tableName.'.'.$refField.' in ('.$this->oDbCon->paramString($pkey,',').')
					 '; 
			}
			
		
			$rs = $this->oDbCon->doQuery($sql);
			 
			$totalRs =  count($rs);
 
			for($i=0;$i<$totalRs;$i++){
 
				$arrNeedle = array();
				$arrReplacement = array();

				foreach($usedParam as $key=>$usedParamRow){   
					$replacement = (isset($rs[$i][$usedParamRow['code']])) ? $rs[$i][$usedParamRow['code']] : '';
					array_push ($arrNeedle,'{{'.$usedParamRow['param'].'}}');
					array_push ($arrReplacement,$replacement);  
				}
				
//				$this->setLog($arrNeedle,true);
//				$this->setLog($arrReplacement,true);
				
				// kalo header 
				if ($row[0]['tableType'] == 0){ 
					$indexField = 'pkey';
					$headerDetail = 'header';
				}elseif($row[0]['tableType'] == 2){ 
					$indexField = 'refkey'; 
					$headerDetail = 'header';
				}else{ 
					$indexField = 'refkey';// semoga as refkey gk nabrk ke field lain 
					$headerDetail = 'detail';
				}
				
				
				$indexkey = $rs[$i][$indexField];
				if(!isset($returnArr[$indexkey])) $returnArr[$indexkey] = array('header' => array(), 'detail' => array());
				
				
				// di replace per line, agar kalo kosong gk jd empty line (utk detail)
				$lines = explode(PHP_EOL, $reportPattern);
				 
				foreach($lines as $line){ 
					$content =  str_replace($arrNeedle,$arrReplacement,$line);
					
					if(!empty($content)){ 
                        if ($row[0]['tableType'] == 1) $content = '>> '. $content;
						array_push($returnArr[$indexkey][$headerDetail], $content);
                    }
				}
				
				  
			}
		}
		  
		 // compile ulang, dari header dulu biar rapi
		   
		 $arr = array();
		 foreach($returnArr as $key=>$row){ 
			
			$temp = array();
			 
			if(!empty($row['header']))  array_push($temp, implode('<br>',array_filter($row['header'])));
			if(!empty($row['detail']))  array_push($temp, implode('<br>',array_filter($row['detail'])));
			   
			 $arr[$key] = implode('<br>', $temp);
		 }
		  
		return $arr;
	}
    
    function htmlEntityDecodeArray(&$arr, $keys = array()){
        // hanya bisa untuk 1 dimensi
         foreach($arr as $key=>$row){
            
            foreach($keys as $keyIndex) 
                $arr[$key][$keyIndex] = html_entity_decode($row[$keyIndex]);
         }
         
    }
    
    function useStorage($storageName){ 
        
        if (defined('TEMP_TABLE_S3')) 
            $updateS3Module = (in_array($this->tableName,TEMP_TABLE_S3)) ? true : false;
        else
            $updateS3Module = false;
        
        return ($updateS3Module && defined("STORAGE") && !empty(STORAGE) && STORAGE['name'] == $storageName) ? true : false;
    }
    
    
    function updateFileAjax($arrInput){
        // $arrInput nanti test kedepanyna bisa support beberapa field berbeda sekaligus tdk


            $arr=array();
            foreach ($_POST as $k => $v) { 
                if (!is_array($v)) $v = trim($v);   
                $arr[$k] = $v;      
            }  

            foreach ($_FILES as $k => $v) { 
                $arr[$k] = $v;    
            }   

        
           $pkey = $arr['pkey'];
 
            // manipulasi data agar sesuai dengan format uploadQueue
            // ini kaya nya masalah kalo ad 2 field

        try{
            
            if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
            
               $arrayToJs = array();
             
            
                // pishain mana yg ad perubahan mana yg tdk
//                 foreach ($arrInput as $inputRow){
//                         
//                    $existingDetailRow  = array();
//                     
//                    $elName = $inputRow['elName']; 
//                    $tableFile =   $inputRow['tableFile'];
//                    $detailPkey = $inputRow['detailPkey']; 
//                    $totalRows = count($detailPkey);
//                       
//                    for($i=0;$i<$totalRows;$i++){
//                        $name = (empty($arr[$elName]['name'][$i])) ? $arr['hid'.$elName][$i] : $arr[$elName]['name'][$i];  
//                        array_push($existingDetailRow,array('pkey' => $detailPkey[$i], 'name' => $name);
//                    }
//
//                     
//                     
//                 }
            
            
            
                // >>>> UPDATE FILE
                foreach ($arrInput as $inputRow){
                    $uploadQueue = array();

                    $elName = $inputRow['elName'];
                    $tableFile =   $inputRow['tableFile'];
                    $uploadFolder = $inputRow['uploadFolder'];
                    $detailPkeyEl = $inputRow['detailPkey'];  
                    $totalRows = count($arr[$elName]['name']);

                    $existingDetailRow  = array();
                    $newDetailRow  = array();
                    
                    for($i=0;$i<$totalRows;$i++){
                        $name = (empty($arr[$elName]['name'][$i])) ? $arr['hid'.$elName][$i] : $arr[$elName]['name'][$i];
                        $detailPkey = $arr[$detailPkeyEl][$i];
                            
                        // baris terakhir yg harusnya disabled ntah kenapa ketarik, biar gk ketarik 
                        if(empty($name)) continue;

                        array_push($uploadQueue, array( 'name' => $name,
                                                        'type' => $arr[$elName]['type'][$i],
                                                        'tmp_name' => $arr[$elName]['tmp_name'][$i],
                                                        'error' => $arr[$elName]['error'][$i],
                                                        'size' => $arr[$elName]['size'][$i],
                                                        'uploadFolder' => $uploadFolder
                                                    ));
                         
                        $this->totalUploadSize += $arr[$elName]['size'][$i];
                        
                        if (!empty($detailPkey))
                            array_push($existingDetailRow,array('pkey' => $detailPkey, 'name' => $name));
                        else
                            array_push($newDetailRow,array('pkey' => 0, 'name' => $name));
                        
                      
                    }

                    	 
                    // jika ad upload file dan size lebih besar dari quota
                    // utk saat ini baru hitung yg S3
                    if($this->totalUploadSize > 0){
                        $security = new Security();
                        $planType = $security->getUserPlanType();
                        $diskUsage = $planType[0]['useddiskusage'] * 1024 * 1024; // dalam bytes
                        $maxDiskUsage = $planType[0]['maxdiskusage'] * 1024 * 1024; // dalam bytes
                        if($diskUsage + $this->totalUploadSize > $maxDiskUsage){
                            throw new Exception(ucfirst($this->errorMsg['limit'][6])); 
                        }  
                    }

                    
                    
    //                $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

                    $result =  $this->onUpdateFileAjax($pkey,$uploadQueue);
                    $arrayToJs = array_merge($arrayToJs, $result);  
                    
                    
                    // >>>>> UPDATE DATABASE
                    
                    $sqlUpdate = array();
                    $existingDetailKey = array_column($existingDetailRow,'pkey');

                    // cari dari data yg dikirim, ada tdk ad tmp_name, selama ad pkey, update saja sesuai dengan 
                    // "name" yg baru
                                   
                    foreach($existingDetailRow as $detailFileRow){
                        $sql = 'update '.$tableFile.'
                                set file =  '.$this->oDbCon->paramString($detailFileRow['name']).' 
                                where refkey = '.$this->oDbCon->paramString($pkey).'  and 
                                      pkey  =  '.$this->oDbCon->paramString($detailFileRow['pkey']);
                        array_push($sqlUpdate,$sql);  
                    }               
                                        
                    // hapus semua yg hidDetailKey nya tdk ad  
                    $sql = 'delete from '.$tableFile.'
                            where refkey = '.$this->oDbCon->paramString($pkey).'  and 
                                  pkey not in ('.$this->oDbCon->paramString($existingDetailKey,',').' ) ';
                    array_push($sqlUpdate,$sql); 

                    
                    // harus dibawah, agar tdk kedelete sql diatas               
                    // selain itu add new     
                    foreach($newDetailRow as $detailFileRow){
                        $sql = 'insert into '.$tableFile.'  (`refkey`, `file`)
                                values ('.$this->oDbCon->paramString($pkey).' , '.$this->oDbCon->paramString($detailFileRow['name']).')';
                        array_push($sqlUpdate,$sql); 
                    }               
                    

                }
            
            foreach($sqlUpdate as $sqlRow){
//                    $this->setLog($sqlRow,true);
                    $this->oDbCon->execute($sqlRow); 
            }
                
            
            $this->oDbCon->endTrans();
            
        }catch(Exception $e){ 
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }	
             
        return $arrayToJs;
        
    }
    
    function onUpdateFileAjax($pkey,$uploadQueue){
         
        $rsResult = $this->uploadQueuingFile($pkey,$uploadQueue);
        
        return $rsResult;
    }
    
   function simplifyDateRange($startDate, $endDate, $format = 'd M Y'){
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Comparison parts (always use fixed format for logic)
        $startDay   = $start->format('d');
        $endDay     = $end->format('d');
        $startMonth = $start->format('m');
        $endMonth   = $end->format('m');
        $startYear  = $start->format('Y');
        $endYear    = $end->format('Y');

        // Custom formatted values
        $startFormatted = $start->format($format);
        $endFormatted   = $end->format($format);

        // Logic to simplify range
        if ($startYear === $endYear && $startMonth === $endMonth) {
            // Same month and year → show day range + one month+year
            $monthYearFormat = preg_replace('/[dDj]/', '', $format); // Remove day part
            $monthYearFormat = trim(preg_replace('/\s+/', ' ', $monthYearFormat)); // Clean extra spaces

            $monthYear = $start->format($monthYearFormat);
            return "$startDay - $endDay $monthYear";
        }

        if ($startYear === $endYear) {
            // Same year, different month → show both day+month, one year
            return "{$start->format('d M')} - {$end->format('d M')} {$startYear}";
        }

        // Different year → full format
        return "$startFormatted - $endFormatted";
    }
     
    function standardizeRegistrationNumber($str){
        $noWhitespace = preg_replace('/\s+/', '', $str);
        return strtoupper($noWhitespace);
    }
    
    function compareNumberEpsilon($a,$b,$epsilon = 0.0000001){
         // tolerance level 
            if (abs($a - $b) < $epsilon) { 
               return true;
            } else { 
               return false;
            }
    }
    
    function SNPlugin($detailRow,$overwrite, $etc,$disabled = false){
        
        $showOptions = ($detailRow['needsn'] == 1) ? true : false;
        
        $rsSN = $this->getSerialNumber($detailRow['pkey']);
        $arrSN = array_column($rsSN, 'serialnumber');

        $_POST['snList[]'] =  implode(chr(13),$arrSN);
                            
       if (!empty($rsSN)){ 
           $txtSN = "<ul  class=\"tag-list\">";

           for($j=0;$j<count($rsSN);$j++) 
               $txtSN .= '<li>'.$rsSN[$j]['serialnumber'].'</li>';

           $txtSN .= "</ul>";
       }

       $displayStyle =  (!$showOptions) ? 'display:none' : '';
       $txtSNDisplay = (!empty($txtSN)) ? 'display:block' : '';
        
       $html = ' <div class="options-row" style="'.$displayStyle.'">
                                <div style="'.$displayStyle.'" class="total-sn-label need-sn">Selisih SN : <span class="total-sn-remaining">0</span></div>
                                <div class="panel form-panel-result" style="'.$txtSNDisplay.'" >
                                    '.$txtSN.'
                                    <div style="clear:both"></div>
                                </div>
                                <div class="panel form-panel" style="display:none; width: 100%">
                                    <div style="font-weight:bold">'.$this->lang['serialNumber'].'</div>
                                    '.$this->inputTextArea('snList[]', array('overwritePost' => $overwrite,'disabled' =>$disabled, 'etc' => 'attr-label="mnv-opt-sn"  style="height:10em;"')).'   
                                    '.$this->inputHidden('hidDetailSNKey[]',array('overwritePost' => $overwrite, 'disabled' =>$disabled,'etc' => $etc)).' 
                                </div>
                               <div class="panel summary-panel" style="width:200px; float:left"></div>
                               <div style="clear:both"></div>  
                             </div>  ';
        return $html;
    }
    
    function HTMLSpecialCharacterForEditor($value){
      return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');   
    }
    
    function getJobInformation($arrPkey){
        // untuk laporan buku besar
        return array();
    }
    
           
    function getFilesForAPI ($arrPkey, $opt=array()){ 
        
        $tableFile = (!isset($opt['tableFile']) || empty($opt['tableFile'])) ? $this->tableFile : $opt['tableFile'];
        $uploadFileFolder = (!isset($opt['uploadFileFolder']) || empty($opt['uploadFileFolder'])) ? $this->uploadFileFolder : $opt['uploadFileFolder']; 
        
        $sql = 'select 
	   			'.$tableFile .'.pkey,
	   			'.$tableFile .'.refkey,
	   			'.$tableFile .'.file 
			  from 
			  	'.$tableFile.'  
			  where   
                '.$tableFile.'.refkey in ('.$this->oDbCon->paramString($arrPkey,',').')';
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
   
		$rs = $this->oDbCon->doQuery($sql);
             
        $total = count($rs);    
        for($i=0;$i<count($rs);$i++){   
            $rs[$i]['file'] = ($this->useStorage) ? 
                                    $this->createPresignedURL(DOMAIN_NAME.'/'.$uploadFileFolder.$rs[$i]['refkey'].'/'.$rs[$i]['file']) :
                                    HTTP_HOST.'download/filename='.$uploadFileFolder.$rs[$i]['refkey'].'/'.$rs[$i]['file'];
        }
         
        return $rs;
     
    }
    
     
}

class Setting extends BaseClass{
 
 
   function __construct(){
		
       parent::__construct();
        
       $this->tableName = '_setting';
       $this->tableNameDetail = '_setting_detail';
       $this->tableNameCategory = '_setting_category';
       $this->tableUserSetting = '_user_setting';
	   $this->securityObject = 'Setting';
	   $this->tableStatus = 'master_status';
	   $this->uploadFolder = "setting/";
       $this->securityObject = 'Setting';
 		 
	   
   }
	
	function getSettingList($catid= '') {
 
		$sql = '
				select
					*
				from 
					' . $this->tableName . ' 
			    where  
					' . $this->tableName . '.show = 1';
			
		if ($catid <> ''){
		$sql .= ' and
				categorykey = '.$this->oDbCon->paramString($catid);
		}
			$sql .= '	order by orderlist asc';
		 
        return $this->oDbCon->doQuery($sql);
	}
  
  
    function getSettingData($criteria = ''){
		$sql = 'select
					'. $this->tableName . '.pkey,
					'. $this->tableName . '.code,
					'. $this->tableName . '.type,
					'. $this->tableName . '.description,
					'.$this->tableName.'.multivalue,
					'.$this->tableUserSetting.'.value
				from 
					' . $this->tableName . ' left join ' . $this->tableUserSetting . ' on ' . $this->tableName . '.pkey  = ' . $this->tableUserSetting . '.settingkey 
				where 
					1=1
				';	
        
        if(!empty($criteria))
            $sql .= ' ' . $criteria;
        
		return $this->oDbCon->doQuery($sql);
	}
	
	function editData($arrParam){
        
			try {
				$arrayToJs =  array();
				 
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
				  
                    $categorykey = '';
                    if (!empty($arrParam['hidSettingCategoryKey']))
                        $categorykey = $arrParam['hidSettingCategoryKey'];
                        
					$rsContent = $this->getSettingList($categorykey);
					 
					for ($k=0;$k<count($rsContent) ; $k++){  
						 
						 if ($rsContent[$k]['multivalue'] == 1){
							 $sql = 'delete from ' . $this->tableNameDetail . ' where refkey = ' .$rsContent[$k]['pkey'] ;
							 $this->oDbCon->execute($sql);
							 
							 $detailSetting = $arrParam[$rsContent[$k]['code']];
							 $labelSetting =  $arrParam[$rsContent[$k]['code'].'Label'];
							 $descSetting =  $arrParam[$rsContent[$k]['code'].'Desc'];
							 $orderListSetting =  $arrParam[$rsContent[$k]['code'].'OrderList'];
                             
							 for($i=0;$i<count($detailSetting);$i++){ 
							 	$value = trim($detailSetting[$i]);
								$label = trim($labelSetting[$i]);
								$desc = trim($descSetting[$i]);
								$rderlist = trim($orderListSetting[$i]);
								if (empty($label))
									continue;
									
								$sql = 'insert into ' . $this->tableNameDetail . ' (`refkey`,`label`,`desc`,`value`,`orderlist`) values ('.$this->oDbCon->paramString($rsContent[$k]['pkey']).', '.$this->oDbCon->paramString($label).','.$this->oDbCon->paramString($desc).', '.$this->oDbCon->paramString($value).', '.$this->oDbCon->paramString($rderlist).') ';  
                                $this->oDbCon->execute($sql); 
							 }
							 
						 }else{ 
						 		  
								//if (!isset($arrParam[$rsContent[$k]['code']]))
								//	continue;
								 
								if ($rsContent[$k]['type'] == 2){ 
									$updateVal = 'value  = '. $this->oDbCon->paramString($this->unFormatNumber($arrParam[$rsContent[$k]['code']]))  ;
								}else if($rsContent[$k]['type'] == 4 || $rsContent[$k]['type'] == 5 ) { 
                                    // 4 utk tipe script, yg harus disave as is, tanpa paramSring
									$updateVal =  'value  = \''. addslashes( $arrParam[$rsContent[$k]['code']]).'\'' ; 
								}else if($rsContent[$k]['type'] == 6) {
								 	$fileName = $this->updateImage($rsContent[$k]['code'], $arrParam['token-item-image-uploader'.$rsContent[$k]['code']], $arrParam['item-image-uploader'.$rsContent[$k]['code']] );  
									$updateVal = 'value  = '. $this->oDbCon->paramString( $fileName)  ; 
								} else{ 
										$updateVal = 'value  = '. $this->oDbCon->paramString( $arrParam[$rsContent[$k]['code']])  ;
								}
								 

                                $sql = '
                                    update
                                        '. $this->tableUserSetting. '
                                    set
                                        '.$updateVal.'
                                    where
                                        settingkey = '.$this->oDbCon->paramString($rsContent[$k]['pkey']).'	 
                                ';
 
                                 $this->oDbCon->execute($sql);

						} 
                        
					 }
						  	 
                
					//$this->addTrail($sql);
					$this->oDbCon->endTrans();  
					$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
							
				
			} catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage());  
			}		
					  
	} 
	  
	 function updateImage($pkey,$token,$arrImage){		 
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token; 
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder; 
		
			
		if(!is_dir($sourcePath)) 
			return; 
		
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
 
 		//delete previous images	    
		$this->deleteAll($destinationPath);   
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage); 
			$this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			return $arrImage[0]; 
		}
		
		return '';
		
	} 

	function getSettingCategory($pkey = ''){
        
        $criteria = '';
        if (!empty($pkey))
            $criteria = ' where pkey = ' . $this->oDbCon->paramString($pkey);
        
		$sql = '
				select 
					*  
				from 
					'.$this->tableNameCategory.'
                '.$criteria.'
				order by orderlist
			';
		  
		return $this->oDbCon->doQuery($sql);
	}
	
	function getDetailByCode ($code){
		$sql = 'select '.$this->tableNameDetail.'.* 
                from '.$this->tableName.','.$this->tableNameDetail.' 
                where '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and '.$this->tableName.'.code = '.$this->oDbCon->paramString($code).' 
                order by '.$this->tableNameDetail.'.orderlist asc';
		return $this->oDbCon->doQuery($sql);
	}
	
	function getInput($rsContent,$forTemplate=false){
		// !$forTemplate
        
		$arrTemp = '';
		$disabled = false;
        $overwritePost = true;
        $script = '';
        
		if ($forTemplate){
			$disabled = true;
            $overwritePost = false;
		}
		if ($rsContent['multivalue']  == 1) 
			$arrTemp = '[]';
		
        $elName = $rsContent['code']. $arrTemp;
        
		if ($rsContent['type'] == INPUT_TYPE['text']) { 
			$inputType = $this->inputText($elName, array('overwritePost' => $overwritePost, 'disabled' => $disabled));
		}
		else if ($rsContent['type'] == INPUT_TYPE['number'] ){
			$inputType =  $this->inputNumber($elName, array('overwritePost' => $overwritePost, 'disabled' => $disabled)); 
		}
		else if ($rsContent['type'] ==  INPUT_TYPE['textarea']  || $rsContent['type'] ==  INPUT_TYPE['raw'] ){
            // 4 utk tipe script, yg harus disave as is, tanpa paramSring
			$inputType = $this->inputTextArea($elName, array('overwritePost' => $overwritePost, 'disabled' => $disabled, 'etc' => 'style="height:14em;"'));
		}	 	
		else if ($rsContent['type'] == INPUT_TYPE['editor']){
			$inputType = $this->inputEditor($elName, array('overwritePost' => $overwrite));
		}	 	
		else if ($rsContent['type'] == INPUT_TYPE['file']  ){								
		   
				$rsItemImage = array(); 
				$value =  $this->loadSetting($rsContent['code']) ;
			 	
            	
				$inputType = '  <div class="item-image-uploader item-image-uploader'.$rsContent['code'].'">
								<ul class="image-list" ></ul>
								<div style="clear:both; height:1em; "></div>
								<div class="file-uploader">	
									<noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
								</div>
							  </div>  ';
							  
            
				if( !empty($value)){
					$rsItemImage[$rsContent['code']]['file'] =  $value;
				
					$sourcePath = $this->defaultDocUploadPath.$this->uploadFolder.$rsContent['code'];
					$destinationPath = $this->uploadTempDoc.$this->uploadFolder.$rsContent['code'];  
				
					$this->deleteAll($destinationPath); 
				
					if(!is_dir($destinationPath)) 
						mkdir ($destinationPath,  0755, true);
							
					$this->fullCopy($sourcePath,$destinationPath); 
					
					$script .= '    var arrImage = Array();
                                    var arrPHPThumbHash = Array();
                                  ';
					for($i=0;$i<count($rsItemImage);$i++) {
						$script .= 'arrImage.push("'.$rsItemImage[$rsContent['code']]['file'].'"); '; 
						$script .= 'arrPHPThumbHash.push("'.getPHPThumbHash($rsItemImage[$rsContent['code']]['file']).'"); '; 
					
                    }
					$script .= ' createImageUploader({"tabID":selectedTab.newPanel[0].id, "name":fileUploaderTarget+"'.$rsContent['code'].'"},{"folder":folder, "token":"'.$rsContent['code'].'", "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false);'; 
                       
                     
				}else{
					$script .= 'createImageUploader({"tabID":selectedTab.newPanel[0].id, "name":fileUploaderTarget+"'.$rsContent['code'].'"},{"folder":folder},false);';
				}
				  
				 
		} 	
		else if ($rsContent['type'] == INPUT_TYPE['select']){
			$inputType = $this->inputSelect($elName, (isset($rsContent['options'])) ? $rsContent['options'] : array(), array('overwritePost' => $overwritePost, 'disabled' => $disabled));
		}	
		else if ($rsContent['type'] == INPUT_TYPE['autocompletejs']){
			$inputType =  $this->inputAutoComplete(array(  
                                                        'element' => array('value' => $elName,
                                                                           'key' => 'hid'.$elName), // sementara key gk bisa dihilagkan, karena diperlukan di js
                                                        'source' => $rsContent['options'], 
                                                        'etc' => $rsContent['etc']
                                                        )
                                                    );  
		} else if ($rsContent['type'] == INPUT_TYPE['autocomplete']){
			$inputType =  $this->inputAutoComplete(array(  
                                                        'element' => array('value' => $elName,
                                                                           'key' => 'hid'.$elName), // sementara key gk bisa dihilagkan, karena diperlukan di js
														   'source' => array(
																			'url' => $rsContent['url'],
																			'data' => array(  'action' =>'searchData' )	
														   ) , 
                                                        'etc' => $rsContent['etc'] 
                                                        )
                                                    );   
			
		}	
		
        
        $script = '<script type="text/javascript">jQuery(document).ready(function(){  '.$script.' }); </script>';
            
		return $inputType . $script;		
	}
   
}
 
class Security extends BaseClass{
   
   function __construct(){ 
		    parent::__construct();  
			$this->securityObject = 'Security';
   }
	  
    function getSecurityChildObject($parentkey){
        if(!is_array($parentkey)) 
            $parentkey = array($parentkey);
        
        $sql = 'select    
                    security_object.pkey,
                    security_object.parentkey,
                    security_object.modulecode,
                    security_object.modulestatus,
                    security_object.modulename
                from 
                    security_object, user_security_object 
                where  
                    security_object.pkey = user_security_object.security_object_key and 
                    user_security_object.statuskey = 1 and 
                    security_object.parentkey in ('.$this->oDbCon->paramString($parentkey,',') .')
                    '; 
        
        $sql .= ' order by security_object.orderlist asc, security_object.modulename asc'; 
        
	  	$rsObject = $this->oDbCon->doQuery($sql);
		
		return $rsObject ;
    }
    
	function generateSecurityObject($categorykey = '', $criteria = ''){
		$sql = 'select    
                    security_object.* 
                from 
                    security_object, user_security_object 
                where  
                    security_object.pkey = user_security_object.security_object_key and 
                    user_security_object.statuskey = 1';
        
       
        $sql .= (!empty($categorykey)) ? ' and categorykey = ' . $this->oDbCon->paramString($categorykey) : '';
        
        if(!empty($criteria)) $sql .= ' ' . $criteria; // utk ambil leaf duank ,gk bis preefined, nanti gk kesave pas di loop ulang
        
        $sql .= ' order by security_object.orderlist asc, security_object.modulename asc';
        
	  	$rsObject = $this->oDbCon->doQuery($sql);
		
		return $rsObject ;
	}
    
    
    function getSecurityObject($pkey){
        $arrPkey = (!is_array($pkey)) ? array($pkey) : $pkey;
        
        $sql = 'select    
                    security_object.* 
                from 
                    security_object
                where
                    pkey in ('.$this->oDbCon->paramString($arrPkey,',').')
                ';
         
	  	return $this->oDbCon->doQuery($sql); 
    }
    
    function getSecurityObjectCategory(){
        $sql = 'select    
                    security_object_category.* 
                from 
                    security_object_category
                order by orderlist asc
                ';
         
	  	return $this->oDbCon->doQuery($sql); 
    }
	
	function getSecurityKey($objectcode){
		$sql = 'select pkey from security_object where modulecode = ' . strtolower($this->oDbCon->paramString($objectcode));
		$rsObject = $this->oDbCon->doQuery($sql);
		
		if (count($rsObject) <=0 )
			return 0;
			
		return $rsObject[0]['pkey'] ;
	}
	
	function hasSecurityAccess($userkey,$objectkey,$statuskey){
		$sql = 'select 
                    security_access.* 
                from 
                    security_access, 
                    user_security_object,
                    employee
			    where
			         security_access.userkey =  employee.pkey and
			         security_access.objectkey =  user_security_object.security_object_key and
                     user_security_object.statuskey = 1 and
			         userkey = '.$this->oDbCon->paramString($userkey).' and 
                     objectkey = ' .$this->oDbCon->paramString($objectkey).' and 
                     security_access.statuskey = ' .$this->oDbCon->paramString( $statuskey);
	    
		$rs =  $this->oDbCon->doQuery($sql);
		 
		if (count($rs) == 0)
			return false;
		else
			return true;
	} 
	
    function hasSecurityRole($rolekey,$objectkey,$statuskey){
		$sql = 'select security_role.* from security_role, template_role
			 where
			security_role.rolekey =  template_role.pkey and
			 rolekey = '.$this->oDbCon->paramString($rolekey).' and objectkey = ' .$this->oDbCon->paramString($objectkey).' and security_role.statuskey = ' .$this->oDbCon->paramString( $statuskey);
	     
		$rs =  $this->oDbCon->doQuery($sql);
		 
		if (count($rs) == 0)
			return false;
		else
			return true;
	} 
	
    function getAllAccessibleMenu($userkey='',$accesskey = array(10)){
        if(empty($userkey))
            $userkey = $this->userkey;
        
        $sql = 'select 
                    lower(security_object.modulecode) as modulecode
               from 
                    security_access, security_object ,user_security_object
               where 
                    security_object.pkey = security_access.objectkey and
                    security_object.pkey = user_security_object.security_object_key and
                    user_security_object.statuskey = 1 and
                    security_access.statuskey in ('.$this->oDbCon->paramString($accesskey,',').') and
                    security_access.userkey = '.$this->oDbCon->paramString($userkey) 
                ;
        
        return $this->oDbCon->doQuery($sql);
                
    }
    
   // security //
   function adminLogin ($userName='',$password='',$token='', $deviceFingerprint = ''){  

        if (empty($userName)){
            $returnArr['valid'] = false;
            $returnArr['message'] = $this->errorMsg[300]; 
		    return $returnArr;
        }
           
       $arr = $this->checkLicense(); 
       if ($arr['valid'] == false)  
           return $arr;  
       
        $returnArr = array();
        $returnArr['data']  = '';
	   
	   // kalo ad karakter aneh2, langsung return false saja
	   $arrSQLInjection = array('\'','*',';','%');
	   foreach($arrSQLInjection as $char){
		   if(strpos($userName, $char) !== false){  
			     $returnArr['valid'] = false;
           		 $returnArr['message'] = $this->errorMsg[300]; 
				 return $returnArr;
		   }
	   }
	   
		$rsLogin = array();   
		 
       
        $returnVal ='
                select 
                    pkey,
                    name,
                    code,
                    isdriver,
                    issales,
                    username,
                    password,
                    email,
                    secretAuth,
                    photofile,
                    appdeviceid
                from
                    employee
                where  
                    username = '.$this->oDbCon->paramString($userName).'  and
                    password = '.$this->oDbCon->paramString(hash('sha256',md5($password))).' and 
                    statuskey = 2 
            ';		 
  
        //if(DOMAIN_NAME == 'mvnet.wintera.co.id') $this->setLog(  date('d M Y H:i') .' => ' .$returnVal,true,'login-log');
       
        $rsLogin = $this->oDbCon->doQuery($returnVal);
 				  
        if (empty($rsLogin)){
            $returnArr['valid'] = false;
            $returnArr['message'] = $this->errorMsg[300]; 
        }else{
            $returnArr['valid'] = true;
            $returnArr['message'] = (empty($rsLogin[0]['secretAuth'])) ? $this->lang['loginSuccessful'] : ''; 
            $returnArr['data'] = $rsLogin[0]; 
            $returnArr['useOTP'] = (empty($rsLogin[0]['secretAuth'])) ? false : true;  
               
            // overwrite, kalo sudah login, dan token cocok, gk perlu cek token lg
            if ($returnArr['useOTP'] && !empty($token)){  

                //require_once '../assets/vendor/autoload.php';      
                require_once DOC_ROOT. '/assets/vendor/autoload.php';   
                $g = new \Google\Authenticator\GoogleAuthenticator();
                
                if ($g->checkCode($rsLogin[0]['secretAuth'], $token)) {
                    $returnArr['useOTP'] = false;  
                } else {
                    $returnArr['useOTP'] = true; 
                    $returnArr['valid'] = false;
                    $returnArr['message'] = $this->errorMsg[305]; 
                }
                
            }
        } 
		 
	   // nanti dulu
	   
//		 // cek jg utk asal IP yg sama, kalo error sampe 10x block.
//		 // karena ad percobaan kalo ganti2 username SQL INJECTION
//		 // idealnya kalo satu kantor, harusnya gk akan sampe errornya loginnya byk
//		  $ipAddress = $rs[0]['ipaddress'];
//
//		 $sql = 'SELECT 
//				* 
//			FROM 
//				'.$this->tableName .' 
//			WHERE 
//				ipaddress = '.$this->oDbCon->paramString($ipAddress).' and 
//				statuskey = 2 and
//				createdon > DATE_SUB(now(), INTERVAL '.$intervalSecond.' SECOND)
//			order by createdon desc
//			limit '. $loginAttemptUntilLockout .'   
//			';
//
//		  $this->setLog($sql,true);
//		  
//		  $rs = $this->oDbCon->doQuery($sql);
//		  
//		  if (count($rs) >= $loginAttemptUntilLockout )
//			  return false;
       
       
        /*if(!IS_DEVELOPMENT) {
            
            // kalo ada marketplace lazada, harus send log
            $marketplace = new Marketplace();
            $arrParam = array();
            $arrParam['name'] = $userName;
            $arrParam['email'] = 'online@marcopolosales.com'; 
            $arrParam['ipAddress'] =  (IS_DEVELOPMENT) ?  '11.163.1.160' :  $_SERVER['REMOTE_ADDR'];  
            $arrParam['deviceFingerprint'] = $deviceFingerprint;
            $arrParam['loginResult'] = ($returnArr['valid']) ? 'success' : 'fail';
            $arrParam['loginMessage'] = $returnArr['message'];

            $marketplace->DataMoatLoginInAllMarketplace($arrParam);

            if ($returnArr['valid']) 
                 $marketplace->DataMoatComputeRiskInAllMarketplace($arrParam);

        }*/
      
       
		return $returnArr;
	}
    
    function getCookiesOTP($deviceFingerprint){
//        $temp = gmdate('U');
//        
//        // test
//        //return  $deviceFingerprint. $this->secretKey;
//        
//        return  gmdate("Y/m/d H:i",$temp) .$deviceFingerprint. $this->secretKey;
    }
    
    function AuthCookies($token){ 
         
//        $md5Hash = $this->getCookiesOTP(DOMAIN_COOKIES);
//        
//        $tokenValid = false;
//        if ($token == $md5Hash){
//              $tokenValid = true;
//        }else{
//            // coba mundurin 1 menit
//            
//        }
//        if($tokenValid){
//            $sql = 'select pkey,userkey from cookies_log 
//                    where cookiesauth = ' .$this->oDbCon->paramString($_COOKIE[DOMAIN_COOKIES]).' and statuskey = 1 and expiredon > now()';
//        
//            $rs =   $this->oDbCon->doQuery($sql);
//            
//            if (!empty($rs)){
//                    // update ulang expired on
//                   try{  
//                        if(!$this->oDbCon->startTrans())
//                            throw new Exception($this->errorMsg[100]);
//
//                        $sql = 'update cookies_log set expiredon = now() + interval 7 day where userkey = '.$this->oDbCon->paramString($rs[0]['userkey']);
//                        $this->oDbCon->execute($sql); 
//
//                       $this->oDbCon->endTrans();
//
//                    }catch (Exception $e){
//                        $this->oDbCon->rollback(); 
//                    }
//
//
//                $employe = new Employee();
//                $result = $employe->getDataRowById($rs[0]['userkey']);
//                return $result;
//            }
//        }
//        
//        return null;
    }
    
	function setCookiesLog($userkey = '', $deviceFingerprint='', $update = false){
//        if(empty($userkey)){
//            if(!isset($this->userkey) || empty($this->userkey)) return;
//            $userkey = $this->userkey;
//        }
//        
//        if (empty($deviceFingerprint)) return;
// 
//         try{  
//			if(!$this->oDbCon->startTrans())
//				throw new Exception($this->errorMsg[100]);
//             
//            if ($update)
//                // update saja semua cookies
//                $sql = 'update cookies_log set expiredon = now() + interval 7 day
//                        where userkey = '.$this->oDbCon->paramString($userkey);
//            else
//                $sql = 'insert into cookies_log (userkey,cookiesauth,expiredon) 
//                        values ('.$this->oDbCon->paramString($userkey).','.$this->oDbCon->paramString($deviceFingerprint).',now() + interval 7 day)';
//           
//            $this->oDbCon->execute($sql);
//           
//            setcookie(DOMAIN_COOKIES, $deviceFingerprint, time()+3600 * 24 * 7);  /* expire in 7 days */
//           
//              
//		   $this->oDbCon->endTrans();
//                      
//        }catch (Exception $e){
//            $this->oDbCon->rollback(); 
//        }
        
    } 
    
	function removeCookiesLog($userkey = '', $deviceFingerprint = ''){
//  
//        if(empty($userkey)){
//            if(!isset($this->userkey) || empty($this->userkey)) return;
//            $userkey = $this->userkey;
//        }
//            
//         try{  
//			if(!$this->oDbCon->startTrans())
//				throw new Exception($this->errorMsg[100]);
//              
//            $sql = 'delete from cookies_log where 
//                   userkey = '.$this->oDbCon->paramString($userkey) ;
//            
//             // ini nanti dipake kalo ad opsi sign out all device
//            if(!empty($deviceFingerprint))
//                $sql .= ' and cookiesauth = ' .$this->oDbCon->paramString($deviceFingerprint);
//               
//           $this->oDbCon->execute($sql);
//       
//              
//		   $this->oDbCon->endTrans();
//                      
//        }catch (Exception $e){
//            $this->oDbCon->rollback(); 
//        }
//        
    } 
    
    function APIGatePass($userkey, $fileContent, $objectcode = '',$statuskey=10, $mimicSessionObj = true, $overwriteAuthToken = ''){
          
        $RETURN_VALUE = array();
        $RETURN_VALUE['response_code'] = 601;
        $RETURN_VALUE['message'] = $this->errorMsg[601]; 
        
        // kalo post bagaimana ? 
        // sudah termasuk cek pass nya bener atau gk ...
         
        $authResult = (!empty($overwriteAuthToken)) ? 
                            $this->checkAPIGETAuth($overwriteAuthToken , $userkey) :
                            $this->checkAPIAuth($this->getHeaders(),$fileContent, $userkey)
                    ;
        
        if ($authResult){  
           
            //cek hak akses ke modul 
            if(!empty($objectcode)){ 
                $objectkey  = $this->getSecurityKey($objectcode);   
                
                if ($this->hasSecurityAccess( $userkey ,$objectkey,$statuskey)) { 
                   if (!empty($mimicSessionObj)){ 
                        
                        $employee = new Employee();
                        $rsEmployee = $employee->getDataRowById($userkey);
                        
                        $_SESSION[$this->loginAdminSession]['id'] = base64_encode($userkey);
                        $_SESSION[$this->loginAdminSession]['name'] = $rsEmployee[0]['name']; 
                        $_SESSION[$this->loginAdminSession]['username'] = $rsEmployee[0]['username']; 
                        $_SESSION[$this->loginAdminSession]['pass'] = $rsEmployee[0]['password']; 
                        $_SESSION[$this->loginAdminSession]['email'] = $rsEmployee[0]['email'];  
 
                        $this->userkey = $userkey; // gk tau utk $security / $obj
                    }
                        
                    return true;
                }else{ 
                    $RETURN_VALUE['message'] = $this->errorMsg[250]; 
                }
            }else{
                return true;
            }
             
        }
 
        
     
        http_response_code($RETURN_VALUE['response_code'] ); 
        echo json_encode($RETURN_VALUE); 
        die;

        return false;
 
    }
    
 
    function APIGatePassV2($userkey, $objectcode = '',$statuskey=10, $setSession = false, $overwriteAuthToken = ''){
          
        $RETURN_VALUE = array();
        $RETURN_VALUE['response_code'] = 601;
        $RETURN_VALUE['message'] = $this->errorMsg[601]; 
        
        // kalo post bagaimana ? 
        // sudah termasuk cek pass nya bener atau gk ...
         
       /* $authResult = (!empty($overwriteAuthToken)) ? 
                            $this->checkAPIGETAuth($overwriteAuthToken , $userkey) :
                            $this->checkAPIAuth($this->getHeaders(),$fileContent, $userkey)
                    ;*/
        
        $authResult =  $this->checkAPIAuthV2($this->getHeaders(), $userkey); 
        
        if ($authResult){
            
            if(!empty($objectcode)){   
                // kalo dikirim object, cek hak akses...
                $objectkey = $this->getSecurityKey($objectcode);   
                $hasAccess = $this->hasSecurityAccess( $userkey ,$objectkey,$statuskey); 
            }else{ 
                $hasAccess = true;
            }
            
            if($hasAccess && !empty($setSession)){
                
                $employee = new Employee();
                $rsEmployee = $employee->getDataRowById($userkey);

                $_SESSION[$this->loginAdminSession]['id'] = base64_encode($userkey);
                $_SESSION[$this->loginAdminSession]['name'] = $rsEmployee[0]['name']; 
                $_SESSION[$this->loginAdminSession]['username'] = $rsEmployee[0]['username']; 
                $_SESSION[$this->loginAdminSession]['pass'] = $rsEmployee[0]['password']; 
                $_SESSION[$this->loginAdminSession]['email'] = $rsEmployee[0]['email'];  

                $this->userkey = $userkey; // gk tau utk $security / $obj
                
                return array(
                    'sessionId' =>  $this->loginAdminSession,
                    'userkey' =>  $userkey,
                ); 
            }else{
                $RETURN_VALUE['message'] = $this->errorMsg[250]; 
            } 
             
        }
 
        
     
        http_response_code($RETURN_VALUE['response_code'] ); 
        echo json_encode($RETURN_VALUE); 
        die;
    }
    
	
    
    function APIGatePassV3($userkey, $objectcode = '',$statuskey=10, $setSession = false, $overwriteAuthToken = '', $accountType = 0){
          
        // $account type untuk membedkan, login pake emplyoee atau customer login
        // karena beda koneksi table, pass dan hak akses
        
        $RETURN_VALUE = array();
        $RETURN_VALUE['response_code'] = 601;
        $RETURN_VALUE['message'] = $this->errorMsg[601]; 
        
        // kalo post bagaimana ? 
        // sudah termasuk cek pass nya bener atau gk ...
         
       /* $authResult = (!empty($overwriteAuthToken)) ? 
                            $this->checkAPIGETAuth($overwriteAuthToken , $userkey) :
                            $this->checkAPIAuth($this->getHeaders(),$fileContent, $userkey)
                    ;*/
        
		//$this->setLog(' >>>  $userkey '. $userkey,true);
        $authResult =  $this->checkAPIAuthV3($this->getHeaders(), $userkey, $accountType); 
        
        if ($authResult){
            
            // bedakan antara customer dan employee 
            if ($accountType == 1){
                $objUser = new Customer();
                
                // utk customer
                $hasAccess = true; // nanti perlu dipikirin lg, 
                
                $sessionId = $this->loginSession;
                
            }else{
                
                $objUser = new Employee();
                
                // utk karyawan 
                if(!empty($objectcode)){   
                    // kalo dikirim object, cek hak akses...
                    $objectkey = $this->getSecurityKey($objectcode);   
                    $hasAccess = $this->hasSecurityAccess( $userkey ,$objectkey,$statuskey); 
                }else{ 
                    $hasAccess = true;
                }
                
                $sessionId = $this->loginAdminSession;
            }
            
            

            if($hasAccess && !empty($setSession)){
 
                $rsUser = $objUser->getDataRowById($userkey);

                $_SESSION[$sessionId]['id'] = base64_encode($userkey);
                $_SESSION[$sessionId]['name'] = $rsUser[0]['name']; 
                $_SESSION[$sessionId]['username'] = $rsUser[0]['username']; 
                $_SESSION[$sessionId]['pass'] = $rsUser[0]['password']; 
                $_SESSION[$sessionId]['email'] = $rsUser[0]['email'];  

                $this->userkey = $userkey; // gk tau utk $security / $obj

                return array(
                    'sessionId' => $sessionId,
                    'userkey' =>  $userkey,
                ); 
            }else{
                $RETURN_VALUE['message'] = $this->errorMsg[250]; 
            } 
             
        }
 
        
     
        http_response_code($RETURN_VALUE['response_code'] ); 
        echo json_encode($RETURN_VALUE); 
        die;
    }
    
    
    function getHeaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers; 
    } 
 
    function isAdminLogin($objectcode,$statuskey=10,$redirect = false ) {
	    
        $arr = $this->checkLicense(); 
		
		
        if ($arr['valid'] == false)
           return false;
          
		if (empty($_SESSION[$this->loginAdminSession])){
			 
			 if ($redirect)
				$this->kick($redirect);
			  else	
				return false;
		}
	  
		$memberkey = base64_decode($_SESSION[$this->loginAdminSession]['id']); 
		
    	$sql = '
			select
				pkey
			from 
				employee
			 where 
			 	employee.pkey=' . $this->oDbCon->paramString($memberkey) . ' and
				employee.password=' . $this->oDbCon->paramString($_SESSION[$this->loginAdminSession]['pass']) . ' and
				statuskey <> 1 
    	';
		
    	$userResultSet = $this->oDbCon->doQuery($sql);
		
    	if( count($userResultSet) == 0 ){
    	  if ($redirect)
		    $this->kick($redirect);
		  else	
			return false;
		}
		
		
		//cek hak akses ke modul 
		$objectkey  = $this->getSecurityKey($objectcode); 
	 
		if (!$this->hasSecurityAccess( $memberkey ,$objectkey,$statuskey)){
		
		  if ($redirect)
		    $this->kick($redirect);
		  else	
			return false;
		}
		
    	return true;
    }
	
	function isMemberLogin($redirect = true) { 
		
		if (empty($_SESSION[$this->loginSession]['id'])){
			 if ($redirect)
				$this->kick($redirect);
			  else	
				return false;
		}
		  
		$loginType = $_SESSION[$this->loginSession]['logintype'];
		
		if(in_array($loginType,array(1))){
			$sql = '
				select
					pkey
				from 
					customer
				 where 
					customer.pkey=' . $this->oDbCon->paramString(base64_decode($_SESSION[$this->loginSession]['id'])) . '
					and username = '. $this->oDbCon->paramString($_SESSION[$this->loginSession]['username']) .'  
					and password = '.$this->oDbCon->paramString($_SESSION[$this->loginSession]['pass']).'
					and statuskey = 2  
			';

		}else{
			$sql = ' select 
						customer.pkey
					from customer ,customer_account_detail
					where 
						customer.pkey = customer_account_detail.refkey and
						customer.pkey=' . $this->oDbCon->paramString(base64_decode($_SESSION[$this->loginSession]['id'])) . ' and
						customer_account_detail.username = '.$this->oDbCon->paramString($_SESSION[$this->loginSession]['username']) .' and 
						customer_account_detail.password = '.$this->oDbCon->paramString($_SESSION[$this->loginSession]['pass']) .' and
						customer.statuskey in (2) 
				';
		}
			
    	
 
		$userResultSet = $this->oDbCon->doQuery($sql);
 
		if(empty($userResultSet)) {
		   if ($redirect){
				 $this->kick(); 
		   } else{
				return false;
			} 
    	}
		
    	return true;
    }
	
	
	
	function kick($redirect = false,$kickurl = '/') { 
		$nonAccessPage = '<script>location.href = "'.$kickurl.'"</script>';
		echo $nonAccessPage;
		die; 
    }
	    
	  
	function searchData($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select security_object.modulename, security_access.userkey, security_access.statuskey  from security_object,security_access where security_object.pkey = security_access.objectkey ';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
         
		return $this->oDbCon->doQuery($sql);	
	}
     
    
    function checkLicense(){ 
        
        //$this->setLog('check license',true,'conn.txt');
		
		$arrayToJs = array(); 
        
        // kalo offline
        $offLineLicenseFile = DOC_ROOT.'license.php';
        
        if(is_file($offLineLicenseFile)){
			
        	require_once($offLineLicenseFile);
            
        	$domainRegistered = (in_array(DOMAIN_NAME,OFFLINE_LICENSE['domain'])) ? true : false;
            
            if(defined('OFFLINE_LICENSE') && $domainRegistered){
                $expiredDate = OFFLINE_LICENSE['expiredDate']; // '2099-01-01';
                $datediff = $this->dateDiff(date('d / m / Y'),$this->formatDBDate($expiredDate));  

                $duedate = round($datediff / (60 * 60 * 24));

                $arrayToJs['duedate'] = $duedate;
                $arrayToJs['valid'] = ($duedate >= 0 && $domainRegistered) ? true : false ;
                
                if ($duedate < 0){ 
                    $arrayToJs['valid'] = false;
                    $arrayToJs['message'] = $this->lang['licenseExpired']; 
                }else if ($duedate <= 14){
                    $arrayToJs['message'] = str_replace('{{duedate}}',$duedate,$this->lang['licenseWillExpireInDays']);  
                } else {
                    $arrayToJs['message'] = str_replace('{{duedate}}',$this->formatDBDate($expiredDate),$this->lang['licenseIsValidUntil']);  
                }                         
 
            }else{ 
                $arrayToJs['duedate'] = 0;
                $arrayToJs['valid'] =  false ;
                $arrayToJs['message'] = $this->lang['invalidLicense'];
            }
                
            return $arrayToJs; 
        }
       
       //$this->setLog('check db license',true,'conn.txt');
        
        //global $DBLicenseCon; 
        $licenseCon = $this->masterConn(); 
        
        $arrayToJs['valid'] = true;
        
        //if license not match with expired date, die. 
        $sql = 'select * from customer_company where name = '. $licenseCon->paramString(DOMAIN_NAME) .' and statuskey = 2';  
        $rsLicense = $licenseCon->doQuery($sql);  
        if (empty($rsLicense)){
            $arrayToJs['valid'] = false;
            $arrayToJs['message'] = $this->lang['invalidLicense']; 
            
            return $arrayToJs;
        } 
        
        $expiredDate = $rsLicense[0]['expireddt']; 
        
        // check if expired  
        $sql = 'select datediff('.$this->oDbCon->paramString($expiredDate).',now()) as duedate  ';
        $rsLicense = $licenseCon->doQuery($sql); 
        
        $duedate = $rsLicense[0]['duedate'];
        
        $arrayToJs['duedate'] = $duedate;
        if ($duedate < 0){ 
            $arrayToJs['valid'] = false;
            $arrayToJs['message'] = $this->lang['licenseExpired']; 
        }else if ($duedate <= 14){
            $arrayToJs['message'] = str_replace('{{duedate}}',$duedate,$this->lang['licenseWillExpireInDays']);  
        } else {
            $arrayToJs['message'] = str_replace('{{duedate}}',$this->formatDBDate($expiredDate),$this->lang['licenseIsValidUntil']);  
        }                        
        
        $licenseCon = null;
        
        return $arrayToJs; 
    }
	 
     function updateAvailableSecurityObject ($dbCon,$plantype){  
        // HANYA PERLU DIAKSES KETIKA PINDAH PLAN 
        
        $securityModule = array();
        $securityModule[1] = array('AutoCode','Brand','City', 'Customer','CityCategory', 'COGS', 'Employee','EmployeeCategory',
                                   'Item','ItemAdjustment','ItemCategory','ItemIn','ItemOut','ItemUnit', 'PurchaseOrder', 'ReportCustomer',
                                   'ReportItem','ReportItemAdjustment','ReportItemIn','ReportItemOut', 'ReportPurchaseOrder', 'ReportPurchaseOrder',
                                   'ReportSalesDelivery','ReportSalesOrder', 'ReportStockCard',
                                   'ReportSupplier', 'RoleTemplate','SalesOrder', 'Setting','Shipment', 'Supplier','Warehouse'   
                                  );

        $securityModule[2] = array('AP','APPayment', 'AR', 'ARPayment','PaymentMethod', 'ReportAP', 'ReportAPPayment', 'ReportAR',
                                   'ReportARPayment', 'ReportWarehouseTransfer','TermOfPayment', 'WarehouseTransfer'
                                  );
        $securityModule[2] = array_merge($securityModule[2],$securityModule[1]);


        $securityModule[3] = array( 'Article', 'ArticleCategory' , 'Banner' , 'CashBankTransfer', 'CashIn','CashOut', 'ChartOfAccount', 'COALink',
                                    'Contact', 'Currency', 'CurrencyRate', 'EmailBlast', 'Event', 'FilterCategory', 'Gallery', 'GeneralJournal',
                                    'ItemFilter','News', 'NewsCategory', 'Page','PaymentConfirmation', 'PurchaseReceive', 'PurchaseReturn',
                                    'ReportBalanceSheet', 'ReportCashBankTransfer','ReportCashFlow','ReportCashIn', 'ReportCashOut',
                                    'ReportGeneralJournal', 'ReportGeneralLedger', 'ReportIncomeStatement', 'ReportItemFilter', 'SalesDelivery',
                                    'SalesReturn', 'Youtube' 
                                  );
        $securityModule[3] = array_merge($securityModule[3],$securityModule[2]);

        //BillingStatement 
        //ItemPromo 
        //Preorder
        //PreorderItem      
        //ReportRewardsPoint
        //RewardsPoint 
        //Survey

        
         try{  
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
             
            // add data yg blm ada
             
            $sql = 'select * from security_object where pkey not in (select security_object_key from user_security_object) ';
            $rs = $dbCon->doQuery($sql);
            
            for($i=0;$i<count($rs);$i++){
                $sql = 'insert into user_security_object (security_object_key, statuskey) values ('.$dbCon->paramString($rs[$i]['pkey']).',1)'; 
                $dbCon->execute($sql);
            }
            
            // cek min akses ke module karyawan harus ad utk user default
            $sql = 'select pkey from employee where systemVariable = 1 order by pkey asc limit 1';
            $rsEmployee =  $dbCon->doQuery($sql);
             
            $sql = 'select * from security_object where modulecode = \'employee\'';
            $rsModule = $dbCon->doQuery($sql);
                
            $sql = 'select * from security_access where userkey = '.$rsEmployee[0]['pkey'].' and objectkey = ' . $rsModule[0]['pkey'] ; 
            $rsTemp =  $dbCon->doQuery($sql);
        
            if (count($rsTemp) <> 6){
                $sql = 'delete from security_access where userkey = '.$rsEmployee[0]['pkey'].' and objectkey = ' . $rsModule[0]['pkey'] ; 
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey, statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',1)';
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey, statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',2)';
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey,  statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',3)';
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey, statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',10)';
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey,  statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',11)';
                $dbCon->execute($sql);
                
                $sql = 'insert into security_access (userkey, objectkey, statuskey) values ('.$rsEmployee[0]['pkey'].','.$rsModule[0]['pkey'].',12)';
                $dbCon->execute($sql); 
            } 
             
             
            $modulecode = implode(',' ,$dbCon->paramString($securityModule[$plantype]) );
             
            $sql = 'update user_security_object set statuskey = 2';
            $dbCon->execute($sql);
             
            $sql = 'update 
                        security_object, user_security_object 
                    set 
                        statuskey = 1
                    where 
                        security_object.pkey = user_security_object.security_object_key and
                        security_object.modulecode in ('.$modulecode.')
                    ';   
            $dbCon->execute($sql);
              
		    $dbCon->endTrans();
                      
        }catch (Exception $e){
            $dbCon->rollback(); 
        }
         
    }
      
    function getUserPlanType(){
           
        $sql = 'select * from '.$this->tablePlanType.' order by pkey desc limit 1 ';
        $rsPlanType = $this->oDbCon->doQuery($sql); 
        return $rsPlanType;
         
    }
    
  }

?>