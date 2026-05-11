<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  

<?php

if (!isset($_FILES) || empty($_FILES['fileToUpload']['tmp_name']))
   die ('<div class="text-red-cardinal" style="padding:1em">Missing File !</div>');
 
 
if (!IS_DEVELOPMENT) { 
    // cek token
    if (!isset($_POST) || empty($_POST['token']))
        die ('<div class="text-red-cardinal" style="padding:1em">Missing Token !</div>');

    //validasi token 
    require_once '../../assets/vendor/autoload.php';     
    $g = new \Google\Authenticator\GoogleAuthenticator();

    $userkey = base64_decode($_SESSION[$class->loginAdminSession]['id']); 
    $rsLogin = $employee->getDataRowById($userkey);
    if (!$g->checkCode($rsLogin[0]['secretAuth'], $_POST['token']))  
        die ('<div class="text-red-cardinal" style="padding:1em">Invalid Token !</div>'); 

}

$inputFileType = 'Xlsx';   
$inputFileName = $_FILES['fileToUpload']['tmp_name']; 
 
$ext = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));

if ($ext <> 'xlsx') die($class->errorMsg[602]);

$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType); 
$reader->setReadDataOnly(true); 
 
$spreadsheet = $reader->load($inputFileName);
$worksheet = $spreadsheet->getActiveSheet();
 
$worksheet = $spreadsheet->getActiveSheet();
// Get the highest row and column numbers referenced in the worksheet
$highestRow = $worksheet->getHighestRow(); // e.g. 10
$highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5


function resetTable($obj,$arrTable){
    
    $obj->oDbCon->startTrans();
    
    for ($i=0;$i<count($arrTable);$i++){
        
        //cek ad nilai default gk
        $sql = 'select * from '.$arrTable[$i].' limit 1';
        $rs =  $obj->oDbCon->doQuery($sql);  
        $criteria = ''; 
        if (isset($rs[0]['systemVariable']))
            $criteria = ' and systemVariable <> 1';
              
        echo '<li class="text-red-cardinal">Deleting <strong>'.$arrTable[$i].'</strong>.</li>'; 
        $sql = 'delete from ' . $arrTable[$i] .' where 1=1 ' . $criteria;
        $obj->oDbCon->execute($sql);     

        echo '<li class="text-green-avocado">Updating key for <strong>'.$arrTable[$i].'</strong>.</li>'; 
        $sql = 'update _nextkey set nextkey = 1 where table_name = \''.$arrTable[$i].'\'';
        $obj->oDbCon->execute($sql);    

        echo '<li class="text-green-avocado">Updating code for <strong>'.$arrTable[$i].'</strong>.</li>'; 
        
        // ambil kode terakhir
        $sql = 'SHOW COLUMNS FROM `'.$arrTable[$i].'` LIKE \'code\'';
        $rs =  $obj->oDbCon->doQuery($sql);     
        if(!empty($rs)){
            $sql = 'select code,digit from _code where code = \''.$arrTable[$i].'\'';
            $rs =  $obj->oDbCon->doQuery($sql);     
            $length = $rs[0]['digit'];

            $sql = 'select code from '.$arrTable[$i].' order by pkey desc limit 1';
            $rs =  $obj->oDbCon->doQuery($sql);  
            if (empty($rs)){
                $codeCtr = 1;
            }else{
                 $codeCtr = (int) substr($rs[0]['code'], $length * -1 ); 
                 $codeCtr++;
            } 

            $sql = 'update _user_code, _code set _user_code.counter = '.$codeCtr.' where _user_code.codekey = _code.pkey and _code.code = \''.$arrTable[$i].'\'';
            $obj->oDbCon->execute($sql);     
        }
        
        
    }

    $obj->oDbCon->endTrans();
} 

function validateSecurity($obj,$moduleName,$spreadsheet){  
    
    $security = new Security();
    
    if(!$security->isAdminLogin($obj->securityObject,11,true)); // kalo gk boleh add, kick !

    $keywords = $spreadsheet->getProperties()->getKeywords();
    
    if (strtolower(IMPORT_TEMPLATE[$moduleName]) <> strtolower($keywords))
        die('<div style="padding:1em" class="text-red-cardinal">'.$obj->errorMsg[602].'</div>');   


    if (isset($_POST) && !empty($_POST['chkReset'])){ 
        if(!$security->isAdminLogin($obj->securityObject,12,false)) 
         die('<div style="padding:1em"  class="text-red-cardinal">'.$obj->errorMsg[251].'</div>');    
    }


}
 

function convertValue($value,$structureRow){
    // test kalo hasil convert gk ketemu, misal nama gudnag ke kode gudang, 
    // tetep balikin nilai awal.
    // MUNGKIN nanti bisa jd otomatis user bisa masukin nama gudang atau kode gudang (tp gk tau di field lain bisa gk)
    
    global $class;  
    $value = trim($value);
    
    $fieldName = $structureRow['field']; 
      
    // convert 
    if (isset($structureRow['convert']) && !empty($structureRow['convert'])){
		
		
        $convert = $structureRow['convert'];
        $obj = $convert['obj'];
        
        $fromColumn = (isset($convert['columnfrom']) && !empty($convert['columnfrom']) ) ? $convert['columnfrom'] : 'name';
        $fromColumn =  $obj->tableName.'.'.$fromColumn;
        
        $toColumn = (isset($convert['columnto']) && !empty($convert['columnto']) ) ? $convert['columnto'] : 'code';
		
        $rs = $obj->searchData($fromColumn,$value); 
         
        $value = (!empty($rs)) ? $rs[0][$toColumn] : $value;
        
    } 
                          
    return $value;
}

function removeUnusedParameter($arrData){
 
	foreach($arrData as $key=>$row){
		foreach($row as $subkey=>$rowField){
			// biar OBJ ny gk kebawa, OBJ yg ad di dalam replace
			if(isset($row[$subkey]['data']['replace']))
				unset($arrData[$key][$subkey]['data']['replace']);
		}
	}
	
	return $arrData;

}

function importData($DATA_STRUCTURE, $importSet){
    
    global $class;
    
    
    $arrData = array();
    
    if($importSet['datatype'] == 'excel'){
        $startRow = 2;
        $endRow = $importSet['highestRow'] + 1;
        $worksheet =  $importSet['worksheet'];
    }else{ 
        $startRow = 0;
        $endRow = count($importSet['dataset']);
        $dataset = $importSet['dataset'];
    }
    
    for ($row = $startRow; $row < $endRow; ++$row) { 
        $arrTemp = array();
        $isEmpty = true;
        
        foreach($DATA_STRUCTURE as $colIndex => $dataStructureRow){
            
            if (!isset($dataStructureRow['field']) || empty($dataStructureRow['field'])) continue; 
             
            $fieldName = $dataStructureRow['field']; 
             
            if($importSet['datatype'] == 'excel'){ 
                 
                $tempValue = $worksheet->getCellByColumnAndRow($colIndex, $row)->getValue();
                
                if(isset($dataStructureRow['format'])){ 
                    switch($dataStructureRow['format']){
                        case 'date':              
                                    $tempValue = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tempValue);
                                    $tempValue = $tempValue->getTimestamp(); 
                    }
                } 
                    
                
                $value = convertValue($tempValue,$dataStructureRow); 
            }else{ 
            
                // kalo jenis nya detail  
                if (isset($dataStructureRow['detail']) && !empty($dataStructureRow['detail'])){  
                    $value = importData($dataStructureRow['detail'], array('datatype' => 'datastructure', 'dataset' => $dataset[$row][$fieldName]));
                   
                }else{ 
                    $tempValue = (isset($dataset[$row][$fieldName])) ? $dataset[$row][$fieldName] : ''; 
                    $value = convertValue($tempValue,$dataStructureRow); 
                } 
            }
            
			// kalo ad ref, utk v3
			if(isset($dataStructureRow['replace'])){
				 
				$newObj = $dataStructureRow['replace']['obj'];
				
				// sementara selalu nyari dr 'name' dulu
				$replaceField = (isset($dataStructureRow['replace']['field'])) ? $dataStructureRow['replace']['field'] : 'name';
				$rsReplace = $newObj->searchDataRow(array($newObj->tableName.'.code'),
												   'and '.$newObj->tableName.'.'.$replaceField.'  = '. $newObj->oDbCon->paramString($value) );
					
//				$newObj->setLog( 'and '.$newObj->tableName.'.'.$replaceField.'  = '. $newObj->oDbCon->paramString($value),true);
//				$newObj->setLog($rsReplace,true);
				$value = $rsReplace[0]['code'];
			}
			
            
            if(!empty($value)) $isEmpty = false; 
            
			// agar &  tetep ke save, gk bisa pake urlencode, spasi jd error
			// kalo perlu lebih dr satu, pake array
            //$arrTemp[$fieldName]['value']  =  (!is_array($value)) ? str_replace('&','+',$value) : $value; 
			//$arrTemp[$fieldName]['value']  =  (!is_array($value)) ? html_entity_decode($value) : $value; 
			
			$arrTemp[$fieldName]['value'] = $value;
            $arrTemp[$fieldName]['data']  = $dataStructureRow;
             
            if(isset( $arrTemp[$fieldName]['data']['convert'] ))
              $arrTemp[$fieldName]['data']['convert'] = '';

        }

        
        // cek jika semua field kosong, lewatin...
        // kemungkin juga dikosongin karena gk mau diupdate.
        // tp bagaimana jadiny kalo utk delete isi field ? tetep harus di post harusnya...
        //if($isEmpty) continue; 
 
 /*       $arrTemp['createdby']['value'] = $_SESSION[$class->loginAdminSession]['id'];
        $arrTemp['createdby']['data'] = array();
 */
        
        //$arrTemp['_userkey']['value'] = base64_decode($_SESSION[$class->loginAdminSession]['id']);
        //$arrTemp['_userkey']['data'] = array();
 
        array_push($arrData, $arrTemp); 
    }
    
	
    return $arrData ;
}

$templateFile = basename($_SERVER['PHP_SELF']); //str_replace('.php','',basename($_SERVER['PHP_SELF']));
$personalizedTemplateFile = PERSONALIZED_DOC_PATH.'tools/import/'.$templateFile;

if(is_file($personalizedTemplateFile)){
	
	require_once($personalizedTemplateFile);
	die;
}

?>